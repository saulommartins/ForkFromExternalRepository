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
* Versao 2.01.6
*
* Fabio Bertoldi - 20130419
*
*/

----------------
-- Ticket #20014
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.acao
      WHERE cod_acao = 2861
          ;
    IF NOT FOUND THEN
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
          ( 2861
          , 314
          , 'FLModelosRREO.php'
          , 'anexo6novo'
          , 56
          , 'Demonstrativo do Resultado Primário'
          , 'Anexo 6'
          , TRUE
          );
        INSERT
          INTO administracao.permissao
             ( numcgm
             , cod_acao
             , ano_exercicio
             )
        SELECT numcgm
             , 2861 AS cod_acao
             , '2013' AS ano_exercicio
          FROM administracao.permissao
         WHERE cod_acao = 2214
        GROUP BY numcgm
             ;
        DELETE
          FROM administracao.permissao
         WHERE cod_acao = 2214
           AND ano_exercicio = '2013'
             ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20016
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.acao
      WHERE cod_acao = 2873
          ;
    IF NOT FOUND THEN
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
          ( 2873
          , 314
          , 'FLModelosRREO.php'
          , 'anexo8novo'
          , 58
          , 'Demonstrativo das Receitas e Despesas com MDE'
          , 'Anexo 8'
          , TRUE
          );
        INSERT
          INTO administracao.permissao
             ( numcgm
             , cod_acao
             , ano_exercicio
             )
        SELECT numcgm
             , 2873 AS cod_acao
             , '2013' AS ano_exercicio
          FROM administracao.permissao
         WHERE cod_acao = 2195
        GROUP BY numcgm
             ;
        DELETE
          FROM administracao.permissao
         WHERE cod_acao = 2195
           AND ano_exercicio = '2013'
             ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20029
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
     ( 2875
     , 315
     , 'FLModelosRGF.php'
     , 'anexo3novo'
     , 53
     , 'Demonstrativo das Garantias e Contragarantias de Valores'
     , 'Anexo 3'
     , TRUE
     );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2875 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 1506
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1506
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20030
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
     ( 2876
     , 315
     , 'FLModelosRGF.php'
     , 'anexo4novo'
     , 54
     , 'Demonstrativo das Operações de Crédito'
     , 'Anexo 4'
     , TRUE
     );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2876 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 1507
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1507
   AND ano_exercicio = '2013'
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 45
     , 'RGF - Anexo 4 - Demonstrativo das Operações de Crédito'
     , 'RGFAnexo4NovoSemestre.rptdesign'
     );
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 46
     , 'RGF - Anexo 4 - Demonstrativo das Operações de Crédito'
     , 'RGFAnexo4NovoQuadrimestre.rptdesign'
     );


----------------
-- Ticket #20028
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
     ( 2877
     , 315
     , 'FLModelosRGF.php'
     , 'anexo2novo'
     , 52
     , 'Demonstrativo da Dívida Consolidada Líquida'
     , 'Anexo 2'
     , TRUE
     );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2877 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 1505
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1505
   AND ano_exercicio = '2013'
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 51
     , 'RGF - Anexo 2 - Demonstrativo da Dívida Consolidada Líquida'
     , 'RGFAnexo2Novo.rptdesign'
     );


CREATE TABLE stn.contas_rgf_2(
    cod_conta         INTEGER         NOT NULL,
    descricao         VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_contas_rgf_2        PRIMARY KEY (cod_conta)
);
GRANT ALL ON stn.contas_rgf_2 TO siamweb;

INSERT INTO stn.contas_rgf_2 VALUES ( 1, 'DÍVIDA CONSOLIDADA: Dívida Mobiliária'                                                      );
INSERT INTO stn.contas_rgf_2 VALUES ( 2, 'DÍVIDA CONSOLIDADA: Dívida Contratual Interna'                                              );
INSERT INTO stn.contas_rgf_2 VALUES ( 3, 'DÍVIDA CONSOLIDADA: Dívida Contratual Externa'                                              );
INSERT INTO stn.contas_rgf_2 VALUES ( 4, 'DÍVIDA CONSOLIDADA: Precatórios Posteriores a 05/05/2000 (inclusive) - Vencidos e Não Pagos');
INSERT INTO stn.contas_rgf_2 VALUES ( 5, 'DÍVIDA CONSOLIDADA: Outras Dívidas'                                                         );
INSERT INTO stn.contas_rgf_2 VALUES ( 6, 'DEDUÇÕES: Restos a Pagar Processados (Exceto Precatórios)'                                  );
INSERT INTO stn.contas_rgf_2 VALUES ( 7, 'PARCELAMENTO DE DÍVIDAS: De Tributos'                                                       );
INSERT INTO stn.contas_rgf_2 VALUES ( 8, 'PARCELAMENTO DE DÍVIDAS: De Contribuições Sociais - Previdenciárias'                        );
INSERT INTO stn.contas_rgf_2 VALUES ( 9, 'PARCELAMENTO DE DÍVIDAS: De Contribuições Sociais - Demais Contribuições Sociais'           );
INSERT INTO stn.contas_rgf_2 VALUES (10, 'PARCELAMENTO DE DÍVIDAS: Do FGTS'                                                           );
INSERT INTO stn.contas_rgf_2 VALUES (11, 'PARCELAMENTO DE DÍVIDAS: Com Instituição Não Financeira'                                    );
INSERT INTO stn.contas_rgf_2 VALUES (12, 'DÍVIDA COM INSTITUIÇÃO FINANCEIRA: Interna'                                                 );
INSERT INTO stn.contas_rgf_2 VALUES (13, 'DÍVIDA COM INSTITUIÇÃO FINANCEIRA: Externa'                                                 );
INSERT INTO stn.contas_rgf_2 VALUES (14, 'Demais Dívidas Contratuais'                                                                 );
INSERT INTO stn.contas_rgf_2 VALUES (15, 'Precatórios Anteriores a 05/05/2000'                                                        );
INSERT INTO stn.contas_rgf_2 VALUES (16, 'Restos a Pagar Não-Processados de Exercícios Anteriores'                                    );
INSERT INTO stn.contas_rgf_2 VALUES (17, 'Antecipações de Receita Orçamentária - ARO'                                                 );
INSERT INTO stn.contas_rgf_2 VALUES (18, 'DÍVIDA CONSOLIDADA PREVIDENCIÁRIA: Passivo Atuarial'                                        );
INSERT INTO stn.contas_rgf_2 VALUES (19, 'DÍVIDA CONSOLIDADA PREVIDENCIÁRIA: Demais Dívidas'                                          );
INSERT INTO stn.contas_rgf_2 VALUES (20, 'DISPONIBILIDADE DE CAIXA BRUTA: Restos a Pagar Processados'                                 );
INSERT INTO stn.contas_rgf_2 VALUES (21, 'Obrigações não Integrantes da Dívida Consolidada'                                           );

CREATE TABLE stn.vinculo_contas_rgf_2(
    cod_conta           INTEGER             NOT NULL,
    cod_plano           INTEGER             NOT NULL,
    exercicio           CHAR(4)             NOT NULL,
    timestamp           TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_vinculo_contas_rgf_2      PRIMARY KEY                                 (cod_conta, exercicio, cod_plano, timestamp),
    CONSTRAINT fk_vinculo_contas_rgf_2_1    FOREIGN KEY                                 (cod_conta)
                                            REFERENCES stn.contas_rgf_2                 (cod_conta),
    CONSTRAINT fk_vinculo_contas_rgf_2_2    FOREIGN KEY                                 (cod_plano, exercicio)
                                            REFERENCES contabilidade.plano_analitica    (cod_plano, exercicio)
);
GRANT ALL ON stn.vinculo_contas_rgf_2 TO siamweb;

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
  ( 2888
  , 406
  , 'FMConfigurarRGF2.php'
  , 'vincular'
  , 23
  , ''
  , 'Vincular Contas RGF 2'
  , TRUE
  );


----------------
-- Ticket #20021
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
     ( 2878
     , 314
     , 'FLModelosRREO.php'
     , 'anexo12novo'
     , 62
     , 'Demonstrativo das Despesas com Saúde - União (Último Bimestre)'
     , 'Anexo 12'
     , TRUE
     );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2878 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2220
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2220
   AND ano_exercicio = '2013'
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 47
     , 'Anexo 12 - Demonstrativo as Despesas com Ações e Serviços Públicos de Saúde (5 Primeiros Bimestres)'
     , 'RREOAnexo12PrimeirosBimestres.rptdesign'
     );
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 48
     , 'Anexo 12 - Demonstrativo as Despesas com Ações e Serviços Públicos de Saúde (Último Bimestre'
     , 'RREOAnexo12UltimoBimestre.rptdesign'
     );


----------------
-- Ticket #20012
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.acao
      WHERE cod_acao = 2882
          ;
    IF NOT FOUND THEN
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
          ( 2882
          , 314
          , 'FLModelosRREO.php'
          , 'anexo4novo'
          , 54
          , 'DEMONSTRATIVO DAS RECEITAS E DESPESAS PREVIDENCIÁIAS DO RPPS'
          , 'Anexo 4'
          , TRUE
          );
        INSERT
          INTO administracao.permissao
             ( numcgm
             , cod_acao
             , ano_exercicio
             )
        SELECT numcgm
             , 2882 AS cod_acao
             , '2013' AS ano_exercicio
          FROM administracao.permissao
         WHERE cod_acao = 2219
        GROUP BY numcgm
             ;
        DELETE
          FROM administracao.permissao
         WHERE cod_acao = 2219
           AND ano_exercicio = '2013'
             ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20009
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.acao
      WHERE cod_acao = 2874
          ;
    IF NOT FOUND THEN
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
          ( 2874
          , 314
          , 'FLModelosRREO.php'
          , 'anexo1novo'
          , 51
          , 'Balanço Orçamentário'
          , 'Anexo 1'
          , TRUE
          );
        INSERT
          INTO administracao.permissao
             ( numcgm
             , cod_acao
             , ano_exercicio
             )
        SELECT numcgm
             , 2874 AS cod_acao
             , '2013' AS ano_exercicio
          FROM administracao.permissao
         WHERE cod_acao = 1501
        GROUP BY numcgm
             ;
        DELETE
          FROM administracao.permissao
         WHERE cod_acao = 1501
           AND ano_exercicio = '2013'
             ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20027
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 50
     , 'RGF - Anexo I - Demonstrativo da Despesa com Pessoal'
     , 'RGFAnexo1Consorcio.rptdesign'
     );


----------------
-- Ticket #20031
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
  ( 2883
  , 315
  , 'FLModelosRGF.php'
  , 'anexo5novo'
  , 55
  , 'Demonstrativo do Resultado Nominal'
  , 'Anexo 5'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2883 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2170
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2170
   AND ano_exercicio = '2013'
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 52
     , 'RGF - Anexo 5 - Demonstrativo do Resultado Nominal'
     , 'RGFAnexo5Novo.rptdesign'
     );

----------------
-- Ticket #20026
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
  ( 2884
  , 315
  , 'FLModelosRGF.php'
  , 'anexo1novo'
  , 51
  , 'Demonstrativo da Despesa com Pessoal'
  , 'Anexo 1'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2884 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 1504
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1504
   AND ano_exercicio = '2013'
     ;
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 53
     , 'RGF - Anexo 1 - Demonstrativo da Despesa com Pessoal'
     , 'RGFAnexo1Novo.rptdesign'
     );


----------------
-- Ticket #19893
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE tablename  = 'temp_transparencia_remuneracao'
        AND schemaname = 'public'
          ;
    IF NOT FOUND THEN
        CREATE TABLE public.temp_transparencia_remuneracao (
            exercicio                   CHAR(4)             NOT NULL,
            cod_entidade                INTEGER             NOT NULL,
            cod_periodo_movimentacao    INTEGER             NOT NULL,
            registro                    INTEGER             NOT NULL,
            cod_contrato                INTEGER             NOT NULL,
            cgm                         VARCHAR(255)        NOT NULL,
            remuneracao_bruta           VARCHAR(30)                 ,
            redutor_teto                VARCHAR(30)                 ,
            remuneracao_natalina        VARCHAR(30)                 ,
            remuneracao_ferias          VARCHAR(30)                 ,
            remuneracao_outras          VARCHAR(30)                 ,
            deducoes_irrf               VARCHAR(30)                 ,
            deducoes_obrigatorias       VARCHAR(30)                 ,
            demais_deducoes             VARCHAR(30)                 ,
            salario_familia             VARCHAR(30)                 ,
            jetons                      VARCHAR(30)                 ,
            verbas                      VARCHAR(30)                 ,
            CONSTRAINT pk_temp_transparencia_remuneracao    PRIMARY KEY                                     (exercicio, cod_entidade, cod_periodo_movimentacao, registro, cod_contrato, cgm),
            CONSTRAINT fk_temp_transparencia_remuneracao_1  FOREIGN KEY                                     (cod_periodo_movimentacao)
                                                            REFERENCES folhapagamento.periodo_movimentacao  (cod_periodo_movimentacao),
            CONSTRAINT fk_temp_transparencia_remuneracao_2  FOREIGN KEY                                     (exercicio, cod_entidade)
                                                            REFERENCES orcamento.entidade                   (exercicio, cod_entidade)
        );
        GRANT ALL ON public.temp_transparencia_remuneracao TO siamweb;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20306
----------------

CREATE TABLE tcmgo.plano_contas_tcmgo(
    cod_plano           INTEGER                 NOT NULL,
    exercicio           CHAR(4)                 NOT NULL,
    estrutural          CHAR(16)                NOT NULL,
    titulo              VARCHAR(120)            NOT NULL,
    natureza            CHAR(1)                 NOT NULL,
    CONSTRAINT pk_plano_contas_tcmgo            PRIMARY KEY (cod_plano, exercicio)
);
GRANT ALL ON tcmgo.plano_contas_tcmgo TO siamweb;

CREATE TABLE tcmgo.vinculo_plano_contas_tcmgo(
    cod_plano           INTEGER                 NOT NULL,
    exercicio           CHAR(4)                 NOT NULL,
    cod_plano_tcmgo     INTEGER                 NOT NULL,
    exercicio_tcmgo     CHAR(4)                 NOT NULL,
    CONSTRAINT pk_vinculo_plano_conta_tcmgo     PRIMARY KEY                          (cod_plano, exercicio, cod_plano_tcmgo, exercicio_tcmgo),
    CONSTRAINT fk_vinculo_plano_conta_tcmgo_1   FOREIGN KEY                          (cod_plano, exercicio)
                                                REFERENCES contabilidade.plano_analitica (cod_plano, exercicio),
    CONSTRAINT fk_vinculo_plano_conta_tcmgo_2   FOREIGN KEY                          (cod_plano_tcmgo, exercicio_tcmgo)
                                                REFERENCES tcmgo.plano_contas_tcmgo  (cod_plano, exercicio)
);
GRANT ALL ON tcmgo.vinculo_plano_contas_tcmgo TO siamweb;


INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   1,'2013','1.0.0.0.0.00.00','ATIVO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   2,'2013','1.1.0.0.0.00.00','ATIVO CIRCULANTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   3,'2013','1.1.1.0.0.00.00','CAIXA E EQUIVALENTES DE CAIXA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   4,'2013','1.1.1.1.0.00.00','CAIXA E EQUIVALENTES DE CAIXA EM MOEDA NACIONAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   5,'2013','1.1.1.1.1.00.00','CAIXA E EQUIVALENTES DE CAIXA EM MOEDA NACIONAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   6,'2013','1.1.1.1.2.00.00','CAIXA E EQUIVALENTES DE CAIXA EM MOEDA NACIONAL - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   7,'2013','1.1.1.2.0.00.00','CAIXA E EQUIVALENTES DE CAIXA EM MOEDA ESTRANGEIRA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   8,'2013','1.1.1.2.1.00.00','CAIXA E EQUIVALENTES DE CAIXA EM MOEDA ESTRANGEIRA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (   9,'2013','1.1.2.0.0.00.00','CRÉDITOS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  10,'2013','1.1.2.1.0.00.00','CLIENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  11,'2013','1.1.2.1.1.00.00','CLIENTES- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  12,'2013','1.1.2.2.0.00.00','CRÉDITOS TRIBUTÁRIOS A RECEBER','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  13,'2013','1.1.2.2.1.00.00','CRÉDITOS TRIBUTÁRIOS A RECEBER - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  14,'2013','1.1.2.2.2.00.00','CRÉDITOS TRIBUTÁRIOS A RECEBER - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  15,'2013','1.1.2.2.3.00.00','CRÉDITOS TRIBUTÁRIOS A RECEBER - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  16,'2013','1.1.2.2.4.00.00','CRÉDITOS TRIBUTÁRIOS A RECEBER - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  17,'2013','1.1.2.2.5.00.00','CRÉDITOS TRIBUTÁRIOS A RECEBER - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  18,'2013','1.1.2.3.0.00.00','DIVIDA ATIVA TRIBUTARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  19,'2013','1.1.2.3.1.00.00','DIVIDA ATIVA TRIBUTARIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  20,'2013','1.1.2.3.2.00.00','DIVIDA ATIVA TRIBUTARIA - INTRA OFSS ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  21,'2013','1.1.2.3.3.00.00','DIVIDA ATIVA TRIBUTARIA - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  22,'2013','1.1.2.3.4.00.00','DIVIDA ATIVA TRIBUTARIA - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  23,'2013','1.1.2.3.5.00.00','DIVIDA ATIVA TRIBUTARIA - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  24,'2013','1.1.2.4.0.00.00','DIVIDA ATIVA NÃO TRIBUTARIA - CLIENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  25,'2013','1.1.2.4.1.00.00','DIVIDA ATIVA NÃO TRIBUTARIA - CLIENTES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  26,'2013','1.1.2.5.0.00.00','CRÉDITOS DE TRANSFERÊNCIAS A RECEBER','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  27,'2013','1.1.2.5.3.00.00','CRÉDITOS DE TRANSFERÊNCIAS A RECEBER - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  28,'2013','1.1.2.5.4.00.00','CRÉDITOS DE TRANSFERÊNCIAS A RECEBER - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  29,'2013','1.1.2.5.5.00.00','CRÉDITOS DE TRANSFERÊNCIAS A RECEBER - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  30,'2013','1.1.2.6.0.00.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  31,'2013','1.1.2.6.1.00.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  32,'2013','1.1.2.6.3.00.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS-INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  33,'2013','1.1.2.6.4.00.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS-INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  34,'2013','1.1.2.6.5.00.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS-INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  35,'2013','1.1.2.9.0.00.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  36,'2013','1.1.2.9.1.00.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  37,'2013','1.1.2.9.2.00.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  38,'2013','1.1.2.9.3.00.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  39,'2013','1.1.2.9.4.00.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  40,'2013','1.1.2.9.5.00.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  41,'2013','1.1.3.0.0.00.00','DEMAIS CRÉDITOS E VALORES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  42,'2013','1.1.3.1.0.00.00','ADIANTAMENTOS CONCEDIDOS A PESSOAL E A TERCEIROS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  43,'2013','1.1.3.1.1.00.00','ADIANTAMENTOS CONCEDIDOS A PESSOAL E A TERCEIROS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  44,'2013','1.1.3.2.0.00.00','TRIBUTOS A RECUPERAR / COMPENSAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  45,'2013','1.1.3.2.1.00.00','TRIBUTOS A RECUPERAR / COMPENSAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  46,'2013','1.1.3.3.0.00.00','CRÉDITOS A RECEBER POR DESCENTRALIZAÇÃO DA PRESTAÇÃO DE SERVIÇOS PÚBLICOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  47,'2013','1.1.3.3.1.00.00','CRÉDITOS A RECEBER POR DESCENTRALIZAÇÃO DA PRESTAÇÃO DE SERVIÇOS PÚBLICOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  48,'2013','1.1.3.4.0.00.00','CRÉDITOS POR DANOS AO PATRIMÔNIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  49,'2013','1.1.3.4.1.00.00','CRÉDITOS POR DANOS AO PATRIMONIO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  50,'2013','1.1.3.5.0.00.00','DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  51,'2013','1.1.3.5.1.00.00','DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  52,'2013','1.1.3.6.0.00.00','DIVIDA ATIVA NÃO TRIBUTARIA - DEMAIS CRÉDITOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  53,'2013','1.1.3.6.1.00.00','DIVIDA ATIVA NÃO TRIBUTARIA - DEMAIS CRÉDITOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  54,'2013','1.1.3.8.0.00.00','OUTROS CRÉDITOS A RECEBER E VALORES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  55,'2013','1.1.3.8.1.00.00','OUTROS CRÉDITOS A RECEBER E VALORES A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  56,'2013','1.1.3.9.0.00.00','(-) AJUSTE DE PERDAS DE DEMAIS CRÉDITOS E VALORES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  57,'2013','1.1.3.9.1.00.00','(-) AJUSTE DE PERDAS DE DEMAIS CRÉDITOS E VALORES A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  58,'2013','1.1.4.0.0.00.00','INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  59,'2013','1.1.4.1.0.00.00','TÍTULOS E VALORES MOBILIÁRIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  60,'2013','1.1.4.1.1.00.00','TÍTULOS E VALORES MOBILIARIOS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  61,'2013','1.1.4.2.0.00.00','APLICAÇÃO TEMPORÁRIA EM METAIS PRECIOSOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  62,'2013','1.1.4.2.1.00.00','APLICAÇÃO TEMPORÁRIA EM METAIS PRECIOSOS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  63,'2013','1.1.4.9.0.00.00','(-) AJUSTE DE PERDAS DE INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  64,'2013','1.1.4.9.1.00.00','(-) AJUSTE DE PERDAS DE INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  65,'2013','1.1.5.0.0.00.00','ESTOQUES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  66,'2013','1.1.5.1.0.00.00','MERCADORIAS PARA REVENDA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  67,'2013','1.1.5.1.1.00.00','MERCADORIAS PARA REVENDA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  68,'2013','1.1.5.2.0.00.00','PRODUTOS E SERVIÇOS ACABADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  69,'2013','1.1.5.2.1.00.00','PRODUTOS E SERVIÇOS ACABADOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  70,'2013','1.1.5.3.0.00.00','PRODUTOS E SERVIÇOS EM ELABORAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  71,'2013','1.1.5.3.1.00.00','PRODUTOS E SERVIÇOS EM ELABORAÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  72,'2013','1.1.5.4.0.00.00','MATÉRIAS-PRIMAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  73,'2013','1.1.5.4.1.00.00','MATÉRIAS-PRIMAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  74,'2013','1.1.5.5.0.00.00','MATÉRIAIS EM TRANSITO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  75,'2013','1.1.5.5.1.00.00','MATÉRIAIS EM TRANSITO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  76,'2013','1.1.5.6.0.00.00','ALMOXARIFADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  77,'2013','1.1.5.6.1.00.00','ALMOXARIFADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  78,'2013','1.1.5.7.0.00.00','ADIANTAMENTOS A FORNECEDORES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  79,'2013','1.1.5.7.1.00.00','ADIANTAMENTOS A FORNECEDORES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  80,'2013','1.1.5.8.0.00.00','OUTROS ESTOQUES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  81,'2013','1.1.5.8.1.00.00','OUTROS ESTOQUES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  82,'2013','1.1.5.9.0.00.00','(-) AJUSTE DE PERDAS DE ESTOQUES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  83,'2013','1.1.5.9.1.00.00','(-) AJUSTE DE PERDAS DE ESTOQUES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  84,'2013','1.1.9.0.0.00.00','VARIAÇÕES PATRIMONIAIS DIMINUTIVAS PAGAS ANTECIPADAMENTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  85,'2013','1.1.9.1.0.00.00','PRÊMIOS DE SEGUROS A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  86,'2013','1.1.9.1.1.00.00','PRÊMIOS DE SEGUROS A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  87,'2013','1.1.9.2.0.00.00','VPD FINANCEIRAS A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  88,'2013','1.1.9.2.1.00.00','VPD FINANCEIRAS A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  89,'2013','1.1.9.3.0.00.00','ASSINATURAS E ANUIDADES A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  90,'2013','1.1.9.3.1.00.00','ASSINATURAS E ANUIDADES A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  91,'2013','1.1.9.4.0.00.00','ALUGUEIS PAGOS A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  92,'2013','1.1.9.4.1.00.00','ALUGUEIS PAGOS A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  93,'2013','1.1.9.5.0.00.00','TRIBUTOS PAGOS A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  94,'2013','1.1.9.5.1.00.00','TRIBUTOS PAGOS A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  95,'2013','1.1.9.6.0.00.00','CONTRIBUIÇÕES CONFEDERATIVAS A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  96,'2013','1.1.9.6.1.00.00','CONTRIBUIÇÕES CONFEDERATIVAS A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  97,'2013','1.1.9.7.0.00.00','BENEFÍCIOS A PESSOAL A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  98,'2013','1.1.9.7.1.00.00','BENEFÍCIOS A PESSOAL A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (  99,'2013','1.1.9.8.0.00.00','DEMAIS VPD A APROPRIAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 100,'2013','1.1.9.8.1.00.00','DEMAIS VPD A APROPRIAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 101,'2013','1.2.0.0.0.00.00','ATIVO NÃO CIRCULANTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 102,'2013','1.2.1.0.0.00.00','ATIVO REALIZÁVEL A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 103,'2013','1.2.1.1.0.00.00','CRÉDITOS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 104,'2013','1.2.1.1.1.00.00','CRÉDITOS A LONGO PRAZO - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 105,'2013','1.2.1.1.1.01.00','CLIENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 106,'2013','1.2.1.1.1.02.00','CRÉDITOS TRIBUTÁRIOS A RECEBER','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 107,'2013','1.2.1.1.1.03.00','DIVIDA ATIVA TRIBUTARIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 108,'2013','1.2.1.1.1.04.00','DIVIDA ATIVA NÃO TRIBUTARIA - CLIENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 109,'2013','1.2.1.1.1.05.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 110,'2013','1.2.1.1.1.99.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 111,'2013','1.2.1.1.2.00.00','CRÉDITOS A LONGO PRAZO - INTRA OFSS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 112,'2013','1.2.1.1.2.02.00','CRÉDITOS TRIBUTÁRIOS A RECEBER','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 113,'2013','1.2.1.1.2.03.00','DIVIDA ATIVA TRIBUTARIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 114,'2013','1.2.1.1.2.05.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 115,'2013','1.2.1.1.2.99.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 116,'2013','1.2.1.1.3.00.00','CRÉDITOS A LONGO PRAZO - INTER OFSS - UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 117,'2013','1.2.1.1.3.02.00','CRÉDITOS TRIBUTÁRIOS A RECEBER','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 118,'2013','1.2.1.1.3.03.00','DIVIDA ATIVA TRIBUTARIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 119,'2013','1.2.1.1.3.05.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 120,'2013','1.2.1.1.3.99.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 121,'2013','1.2.1.1.4.00.00','CRÉDITOS A LONGO PRAZO - INTER OFSS - ESTADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 122,'2013','1.2.1.1.4.02.00','CRÉDITOS TRIBUTÁRIOS A RECEBER','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 123,'2013','1.2.1.1.4.03.00','DIVIDA ATIVA TRIBUTARIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 124,'2013','1.2.1.1.4.05.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 125,'2013','1.2.1.1.4.99.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 126,'2013','1.2.1.1.5.00.00','CRÉDITOS A LONGO PRAZO - INTER OFSS - MUNICÍPIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 127,'2013','1.2.1.1.5.02.00','CRÉDITOS TRIBUTÁRIOS A RECEBER','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 128,'2013','1.2.1.1.5.03.00','DIVIDA ATIVA TRIBUTARIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 129,'2013','1.2.1.1.5.05.00','EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 130,'2013','1.2.1.1.5.99.00','(-) AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 131,'2013','1.2.1.2.0.00.00','DEMAIS CRÉDITOS E VALORES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 132,'2013','1.2.1.2.1.00.00','DEMAIS CRÉDITOS E VALORES A LONGO PRAZO - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 133,'2013','1.2.1.2.1.01.00','ADIANTAMENTOS CONCEDIDOS A PESSOAL E A TERCEIROS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 134,'2013','1.2.1.2.1.02.00','TRIBUTOS A RECUPERAR / COMPENSAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 135,'2013','1.2.1.2.1.03.00','CRÉDITOS A RECEBER POR DESCENTRALIZAÇÃO DA PRESTAÇÃO DE SERVIÇOS PÚBLICOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 136,'2013','1.2.1.2.1.04.00','CRÉDITOS POR DANOS AO PATRIMÔNIO PROVENIENTES DE CRÉDITOS ADMINISTRATIVOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 137,'2013','1.2.1.2.1.05.00','CRÉDITOS POR DANOS AO PATRIMÔNIO APURADOS EM TOMADA DE CONTAS ESPECIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 138,'2013','1.2.1.2.1.06.00','DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 139,'2013','1.2.1.2.1.07.00','DIVIDA ATIVA NÃO TRIBUTARIA - DEMAIS CRÉDITOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 140,'2013','1.2.1.2.1.98.00','OUTROS CRÉDITOS A RECEBER E VALORES A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 141,'2013','1.2.1.2.1.99.00','(-) AJUSTE DE PERDAS DE DEMAIS CRÉDITOS E VALORES A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 142,'2013','1.2.1.3.0.00.00','INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 143,'2013','1.2.1.3.1.00.00','INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS A LONGO PRAZO - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 144,'2013','1.2.1.3.1.01.00','TÍTULOS E VALORES MOBILIÁRIOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 145,'2013','1.2.1.3.1.02.00','APLICAÇÃO TEMPORÁRIA EM METAIS PRECIOSOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 146,'2013','1.2.1.3.1.03.00','APLICAÇÕES EM SEGMENTO DE IMÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 147,'2013','1.2.1.3.1.99.00','(-) AJUSTE DE PERDAS DE INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS A LONGO PRAZO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 148,'2013','1.2.1.4.0.00.00','ESTOQUES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 149,'2013','1.2.1.4.1.00.00','ESTOQUES - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 150,'2013','1.2.1.4.1.01.00','MERCADORIAS PARA REVENDA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 151,'2013','1.2.1.4.1.02.00','PRODUTOS E SERVIÇOS ACABADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 152,'2013','1.2.1.4.1.03.00','PRODUTOS E SERVIÇOS EM ELABORAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 153,'2013','1.2.1.4.1.04.00','MATÉRIAS-PRIMAS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 154,'2013','1.2.1.4.1.05.00','MATÉRIAIS EM TRANSITO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 155,'2013','1.2.1.4.1.06.00','ALMOXARIFADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 156,'2013','1.2.1.4.1.07.00','ADIANTAMENTOS A FORNECEDORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 157,'2013','1.2.1.4.1.98.00','OUTROS ESTOQUES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 158,'2013','1.2.1.4.1.99.00','(-) AJUSTE DE PERDAS DE ESTOQUES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 159,'2013','1.2.1.9.0.00.00','VARIAÇÕES PATRIMONIAIS DIMINUTIVAS PAGAS ANTECIPADAMENTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 160,'2013','1.2.1.9.1.00.00','VARIAÇÕES PATRIMONIAIS DIMINUTIVAS PAGAS ANTECIPADAMENTE- CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 161,'2013','1.2.1.9.1.01.00','PRÊMIOS DE SEGUROS A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 162,'2013','1.2.1.9.1.02.00','VPD FINANCEIRAS A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 163,'2013','1.2.1.9.1.03.00','ASSINATURAS E ANUIDADES A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 164,'2013','1.2.1.9.1.04.00','ALUGUEIS PAGOS A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 165,'2013','1.2.1.9.1.05.00','TRIBUTOS PAGOS A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 166,'2013','1.2.1.9.1.06.00','CONTRIBUIÇÕES CONFEDERATIVAS A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 167,'2013','1.2.1.9.1.07.00','BENEFÍCIOS A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 168,'2013','1.2.1.9.1.99.00','DEMAIS VPD A APROPRIAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 169,'2013','1.2.2.0.0.00.00','INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 170,'2013','1.2.2.1.0.00.00','PARTICIPAÇÕES PERMANENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 171,'2013','1.2.2.1.1.00.00','PARTICIPAÇÕES PERMANENTES - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 172,'2013','1.2.2.1.1.01.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 173,'2013','1.2.2.1.1.02.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE CUSTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 174,'2013','1.2.2.1.2.00.00','PARTICIPAÇÕES PERMANENTES - INTRA OFSS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 175,'2013','1.2.2.1.2.01.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 176,'2013','1.2.2.1.2.02.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE CUSTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 177,'2013','1.2.2.1.3.00.00','PARTICIPAÇÕES PERMANENTES - INTER OFSS - UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 178,'2013','1.2.2.1.3.01.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 179,'2013','1.2.2.1.3.02.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE CUSTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 180,'2013','1.2.2.1.4.00.00','PARTICIPAÇÕES PERMANENTES - INTER OFSS - ESTADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 181,'2013','1.2.2.1.4.01.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 182,'2013','1.2.2.1.4.02.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE CUSTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 183,'2013','1.2.2.1.5.00.00','PARTICIPAÇÕES PERMANENTES - INTER OFSS - MUNICÍPIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 184,'2013','1.2.2.1.5.01.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 185,'2013','1.2.2.1.5.02.00','PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE CUSTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 186,'2013','1.2.2.2.0.00.00','PROPRIEDADES PARA INVESTIMENTO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 187,'2013','1.2.2.2.1.00.00','PROPRIEDADES PARA INVESTIMENTO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 188,'2013','1.2.2.3.0.00.00','INVESTIMENTOS DO RPPS DE LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 189,'2013','1.2.2.3.1.00.00','INVESTIMENTOS DO RPPS DE LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 190,'2013','1.2.2.7.0.00.00','DEMAIS INVESTIMENTOS PERMANENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 191,'2013','1.2.2.7.1.00.00','DEMAIS INVESTIMENTOS PERMANENTES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 192,'2013','1.2.2.8.0.00.00','(-) DEPRECIAÇÃO ACUMULADA DE INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 193,'2013','1.2.2.8.1.00.00','(-) DEPRECIAÇÃO ACUMULADA DE INVESTIMENTOS - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 194,'2013','1.2.2.8.1.01.00','(-) DEPRECIAÇÃO ACUMULADA DE INVESTIMENTOS - CONSOLIDAÇÃO - PROPRIEDADES PARA INVESTIMENTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 195,'2013','1.2.2.9.0.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 196,'2013','1.2.2.9.1.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 197,'2013','1.2.2.9.1.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - PARTICIPAÇÕES PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 198,'2013','1.2.2.9.1.02.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE PROPRIEDADES PARA INVESTIMENTO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 199,'2013','1.2.2.9.1.03.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS DO RPPS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 200,'2013','1.2.2.9.1.04. 00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - DEMAIS INVESTIMENTOS PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 201,'2013','1.2.2.9.2.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS-INTRA OFSS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 202,'2013','1.2.2.9.2.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - PARTICIPAÇÕES PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 203,'2013','1.2.2.9.2.04.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - DEMAIS INVESTIMENTOS PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 204,'2013','1.2.2.9.3.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS-INTER OFSS - UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 205,'2013','1.2.2.9.3.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - PARTICIPAÇÕES PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 206,'2013','1.2.2.9.3.04.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - DEMAIS INVESTIMENTOS PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 207,'2013','1.2.2.9.4.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS-INTER OFSS - ESTADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 208,'2013','1.2.2.9.4.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - PARTICIPAÇÕES PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 209,'2013','1.2.2.9.4.04.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - DEMAIS INVESTIMENTOS PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 210,'2013','1.2.2.9.5.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS-INTER OFSS - MUNICÍPIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 211,'2013','1.2.2.9.5.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - PARTICIPAÇÕES PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 212,'2013','1.2.2.9.5.04.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS - DEMAIS INVESTIMENTOS PERMANENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 213,'2013','1.2.3.0.0.00.00','IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 214,'2013','1.2.3.1.0.00.00','BENS MOVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 215,'2013','1.2.3.1.1.00.00','BENS MOVEIS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 216,'2013','1.2.3.2.0.00.00','BENS IMÓVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 217,'2013','1.2.3.2.1.00.00','BENS IMOVEIS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 218,'2013','1.2.3.8.0.00.00','(-) DEPRECIAÇÃO, EXAUSTÃO E AMORTIZAÇÃO ACUMULADAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 219,'2013','1.2.3.8.1.00.00','(-) DEPRECIAÇÃO, EXAUSTÃO E AMORTIZAÇÃO ACUMULADAS - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 220,'2013','1.2.3.8.1.01.00','(-) DEPRECIAÇÃO ACUMULADA - BENS MÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 221,'2013','1.2.3.8.1.02.00','(-) DEPRECIAÇÃO ACUMULADA - BENS IMÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 222,'2013','1.2.3.8.1.03.00','(-) EXAUSTÃO ACUMULADA - BENS MÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 223,'2013','1.2.3.8.1.04.00','(-) EXAUSTÃO ACUMULADA - BENS IMÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 224,'2013','1.2.3.8.1.05.00','(-) AMORTIZAÇÃO ACUMULADA - BENS MÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 225,'2013','1.2.3.8.1.06.00','(-) AMORTIZAÇÃO ACUMULADA - BENS IMÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 226,'2013','1.2.3.9.0.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 227,'2013','1.2.3.9.1.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE IMOBILIZADO - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 228,'2013','1.2.3.9.1.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE IMOBILIZADO - BENS MOVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 229,'2013','1.2.3.9.1.02.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE IMOBILIZADO - BENS IMÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 230,'2013','1.2.4.0.0.00.00','INTANGÍVEL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 231,'2013','1.2.4.1.0.00.00','SOFTWARES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 232,'2013','1.2.4.1.1.00.00','SOFTWARES- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 233,'2013','1.2.4.2.0.00.00','MARCAS, DIREITOS E PATÉNTES INDUSTRIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 234,'2013','1.2.4.2.1.00.00','MARCAS, DIREITOS E PATÉNTES INDUSTRIAIS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 235,'2013','1.2.4.3.0.00.00','DIREITO DE USO DE IMÓVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 236,'2013','1.2.4.3.1.00.00','DIREITO DE USO DE IMOVEIS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 237,'2013','1.2.4.8.0.00.00','(-) AMORTIZAÇÃO ACUMULADA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 238,'2013','1.2.4.8.1.00.00','(-) AMORTIZAÇÃO ACUMULADA- CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 239,'2013','1.2.4.8.1.01.00','(-) AMORTIZAÇÃO ACUMULADA - SOFTWARES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 240,'2013','1.2.4.8.1.02.00','(-) AMORTIZAÇÃO ACUMULADA - MARCAS, DIREITOS E PATÉNTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 241,'2013','1.2.4.8.1.03.00','(-) AMORTIZAÇÃO ACUMULADA - DIREITO DE USO DE IMÓVEIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 242,'2013','1.2.4.9.0.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INTANGÍVEL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 243,'2013','1.2.4.9.1.00.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INTANGÍVEL - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 244,'2013','1.2.4.9.1.01.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INTANGÍVEL - SOFTWARES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 245,'2013','1.2.4.9.1.02.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INTANGÍVEL - MARCAS, DIREITOS E PATÉNTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 246,'2013','1.2.4.9.1.03.00','(-) REDUÇÃO AO VALOR RECUPERÁVEL DE INTANGÍVEL-DIREITO DE USO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 247,'2013','2.0.0.0.0.00.00','PASSIVO E PATRIMÔNIO LIQUIDO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 248,'2013','2.1.0.0.0.00.00','PASSIVO CIRCULANTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 249,'2013','2.1.1.0.0.00.00','OBRIGAÇÕES TRABALHISTAS, PREVIDENCIÁRIAS E ASSISTENCIAIS A PAGAR A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 250,'2013','2.1.1.1.0.00.00','PESSOAL A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 251,'2013','2.1.1.1.1.00.00','PESSOAL A PAGAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 252,'2013','2.1.1.2.0.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 253,'2013','2.1.1.2.1.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 254,'2013','2.1.1.2.2.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR- INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 255,'2013','2.1.1.2.3.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR- INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 256,'2013','2.1.1.2.4.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR- INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 257,'2013','2.1.1.2.5.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR- INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 258,'2013','2.1.1.3.0.00.00','BENEFÍCIOS ASSISTENCIAIS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 259,'2013','2.1.1.3.1.00.00','BENEFÍCIOS ASSISTENCIAIS A PAGAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 260,'2013','2.1.1.4.0.00.00','ENCARGOS SOCIAIS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 261,'2013','2.1.1.4.1.00.00','ENCARGOS SOCIAIS A PAGAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 262,'2013','2.1.1.4.2.00.00','ENCARGOS SOCIAIS A PAGAR-INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 263,'2013','2.1.1.4.3.00.00','ENCARGOS SOCIAIS A PAGAR-INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 264,'2013','2.1.1.4.4.00.00','ENCARGOS SOCIAIS A PAGAR-INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 265,'2013','2.1.1.4.5.00.00','ENCARGOS SOCIAIS A PAGAR-INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 266,'2013','2.1.2.0.0.00.00','EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 267,'2013','2.1.2.1.0.00.00','EMPRÉSTIMOS A CURTO PRAZO - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 268,'2013','2.1.2.1.1.00.00','EMPRÉSTIMOS A CURTO PRAZO - INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 269,'2013','2.1.2.1.3.00.00','EMPRÉSTIMOS A CURTO PRAZO - INTERNO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 270,'2013','2.1.2.1.4.00.00','EMPRÉSTIMOS A CURTO PRAZO - INTERNO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 271,'2013','2.1.2.1.5.00.00','EMPRÉSTIMOS A CURTO PRAZO - INTERNO-INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 272,'2013','2.1.2.2.0.00.00','EMPRÉSTIMOS A CURTO PRAZO - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 273,'2013','2.1.2.2.1.00.00','EMPRÉSTIMOS A CURTO PRAZO- EXTERNO CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 274,'2013','2.1.2.3.0.00.00','FINANCIAMENTOS A CURTO PRAZO - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 275,'2013','2.1.2.3.1.00.00','FINANCIAMENTOS A CURTO PRAZO- INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 276,'2013','2.1.2.3.3.00.00','FINANCIAMENTOS A CURTO PRAZO- INTERNO -INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 277,'2013','2.1.2.3.4.00.00','FINANCIAMENTOS A CURTO PRAZO - INTERNO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 278,'2013','2.1.2.3.5.00.00','FINANCIAMENTOS A CURTO PRAZO - INTERNO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 279,'2013','2.1.2.4.0.00.00','FINANCIAMENTO A CURTO PRAZO - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 280,'2013','2.1.2.4.1.00.00','FINANCIAMENTO A CURTO PRAZO - EXTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 281,'2013','2.1.2.5.0.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 282,'2013','2.1.2.5.1.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 283,'2013','2.1.2.5.3.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - INTERNO -INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 284,'2013','2.1.2.5.4.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - INTERNO -INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 285,'2013','2.1.2.5.5.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - INTERNO -INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 286,'2013','2.1.2.6.0.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 287,'2013','2.1.2.6.1.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO - EXTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 288,'2013','2.1.2.8.0.00.00','(-) ENCARGOS FINANCEIROS A APRORIAR - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 289,'2013','2.1.2.8.1.00.00','(-) ENCARGOS FINANCEIROS A APRORIAR - INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 290,'2013','2.1.2.8.3.00.00','(-) ENCARGOS FINANCEIROS A APRORIAR - INTERNO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 291,'2013','2.1.2.8.4.00.00','(-) ENCARGOS FINANCEIROS A APRORIAR - INTERNO - INTER OFFS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 292,'2013','2.1.2.8.5.00.00','(-) ENCARGOS FINANCEIROS A APRORIAR - INTERNO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 293,'2013','2.1.2.9.0.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 294,'2013','2.1.2.9.1.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 295,'2013','2.1.3.0.0.00.00','FORNECEDORES E CONTAS A PAGAR A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 296,'2013','2.1.3.1.0.00.00','FORNECEDORES E CONTAS A PAGAR NACIONAIS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 297,'2013','2.1.3.1.1.00.00','FORNECEDORES E CONTAS A PAGAR NACIONAIS A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 298,'2013','2.1.3.2.0.00.00','FORNECEDORES E CONTAS A PAGAR ESTRANGEIROS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 299,'2013','2.1.3.2.1.00.00','FORNECEDORES E CONTAS A PAGAR ESTRANGEIROS A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 300,'2013','2.1.4.0.0.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 301,'2013','2.1.4.1.0.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO COM A UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 302,'2013','2.1.4.1.1.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO COM A UNIÃO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 303,'2013','2.1.4.2.0.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO COM OS ESTADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 304,'2013','2.1.4.2.1.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO COM OS ESTADOS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 305,'2013','2.1.4.3.0.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO COM OS MUNICÍPIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 306,'2013','2.1.4.3.1.00.00','OBRIGAÇÕES FISCAIS A CURTO PRAZO COM OS MUNICÍPIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 307,'2013','2.1.5.0.0.00.00','OBRIGAÇÕES DE REPARTIÇÃO A OUTROS ENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 308,'2013','2.1.5.0.3.00.00','OBRIGAÇÕES DE REPARTIÇÃO A OUTROS ENTES - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 309,'2013','2.1.5.0.4.00.00','OBRIGAÇÕES DE REPARTIÇÃO A OUTROS ENTES - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 310,'2013','2.1.5.0.5.00.00','OBRIGAÇÕES DE REPARTIÇÃO A OUTROS ENTES - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 311,'2013','2.1.7.0.0.00.00','PROVISÕES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 312,'2013','2.1.7.1.0.00.00','PROVISÃO PARA RISCOS TRABALHISTAS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 313,'2013','2.1.7.1.1.00.00','PROVISÃO PARA RISCOS TRABALHISTAS A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 314,'2013','2.1.7.3.0.00.00','PROVISÕES PARA RISCOS FISCAIS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 315,'2013','2.1.7.3.1.00.00','PROVISÕES PARA RISCOS FISCAIS A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 316,'2013','2.1.7.4.0.00.00','PROVISÃO PARA RISCOS CÍVEIS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 317,'2013','2.1.7.4.1.00.00','PROVISÃO PARA RISCOS CÍVEIS A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 318,'2013','2.1.7.5.0.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 319,'2013','2.1.7.5.3.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A CURTO PRAZO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 320,'2013','2.1.7.5.4.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A CURTO PRAZO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 321,'2013','2.1.7.5.5.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A CURTO PRAZO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 322,'2013','2.1.7.6.0.00.00','PROVISÃO PARA RISCOS DECORRENTES DE CONTRATOS DE PPP A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 323,'2013','2.1.7.6.1.00.00','PROVISÃO PARA RISCOS DECORRENTES DE CONTRATOS DE PPP A CURTO PRAZO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 324,'2013','2.1.7.9.0.00.00','OUTRAS PROVISÕES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 325,'2013','2.1.7.9.1.00.00','OUTRAS PROVISÕES A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 326,'2013','2.1.8.0.0.00.00','DEMAIS OBRIGAÇÕES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 327,'2013','2.1.8.1.0.00.00','ADIANTAMENTOS DE CLIENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 328,'2013','2.1.8.1.1.00.00','ADIANTAMENTOS DE CLIENTES- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 329,'2013','2.1.8.2.0.00.00','OBRIGAÇÕES POR DANOS A TERCEIROS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 330,'2013','2.1.8.2.1.00.00','OBRIGAÇÕES POR DANOS A TERCEIROS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 331,'2013','2.1.8.3.0.00.00','ARRENDAMENTO OPERACIONAL A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 332,'2013','2.1.8.3.1.00.00','ARRENDAMENTO OPERACIONAL A PAGAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 333,'2013','2.1.8.4.0.00.00','DEBÊNTURES E OUTROS TÍTULOS DE DIVIDA A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 334,'2013','2.1.8.4.1.00.00','DEBÊNTURES E OUTROS TÍTULOS DE DIVIDA A CURTO PRAZO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 335,'2013','2.1.8.5.0.00.00','DIVIDENDOS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 336,'2013','2.1.8.5.1.00.00','DIVIDENDOS A PAGAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 337,'2013','2.1.8.8.0.00.00','VALORES RESTITUÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 338,'2013','2.1.8.8.1.00.00','VALORES RESTITUÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 339,'2013','2.1.8.9.0.00.00','OUTRAS OBRIGAÇÕES A CURTO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 340,'2013','2.1.8.9.1.00.00','OUTRAS OBRIGAÇÕES A CURTO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 341,'2013','2.1.8.9.2.00.00','OUTRAS OBRIGAÇÕES A CURTO PRAZO-INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 342,'2013','2.2.0.0.0.00.00','PASSIVO NAO-CIRCULANTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 343,'2013','2.2.1.0.0.00.00','OBRIGAÇÕES TRABALHISTAS, PREVIDENCIÁRIAS E ASSISTENCIAIS A PAGAR A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 344,'2013','2.2.1.1.0.00.00','PESSOAL A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 345,'2013','2.2.1.1.1.00.00','PESSOAL A PAGAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 346,'2013','2.2.1.2.0.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 347,'2013','2.2.1.2.1.00.00','BENEFÍCIOS PREVIDENCIÁRIOS A PAGAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 348,'2013','2.2.1.3.0.00.00','BENEFÍCIOS ASSISTENCIAIS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 349,'2013','2.2.1.3.1.00.00','BENEFÍCIOS ASSISTENCIAIS A PAGAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 350,'2013','2.2.1.4.0.00.00','ENCARGOS SOCIAIS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 351,'2013','2.2.1.4.1.00.00','ENCARGOS SOCIAIS A PAGAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 352,'2013','2.2.1.4.2.00.00','ENCARGOS SOCIAIS A PAGAR-INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 353,'2013','2.2.1.4.3.00.00','ENCARGOS SOCIAIS A PAGAR-INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 354,'2013','2.2.1.4.4.00.00','ENCARGOS SOCIAIS A PAGAR-INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 355,'2013','2.2.1.4.5.00.00','ENCARGOS SOCIAIS A PAGAR-INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 356,'2013','2.2.2.0.0.00.00','EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 357,'2013','2.2.2.1.0.00.00','EMPRÉSTIMOS A LONGO PRAZO - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 358,'2013','2.2.2.1.1.00.00','EMPRÉSTIMOS A LONGO PRAZO - INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 359,'2013','2.2.2.1.3.00.00','EMPRÉSTIMOS A LONGO PRAZO - INTERNO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 360,'2013','2.2.2.1.4.00.00','EMPRÉSTIMOS A LONGO PRAZO - INTERNO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 361,'2013','2.2.2.1.5.00.00','EMPRÉSTIMOS A LONGO PRAZO - INTERNO-INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 362,'2013','2.2.2.2.0.00.00','EMPRÉSTIMOS A LONGO PRAZO - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 363,'2013','2.2.2.2.1.00.00','EMPRÉSTIMOS A LONGO PRAZO- EXTERNO CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 364,'2013','2.2.2.3.0.00.00','FINANCIAMENTOS A LONGO PRAZO - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 365,'2013','2.2.2.3.1.00.00','FINANCIAMENTOS A LONGO PRAZO- INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 366,'2013','2.2.2.3.3.00.00','FINANCIAMENTOS A LONGO PRAZO- INTERNO -INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 367,'2013','2.2.2.3.4.00.00','FINANCIAMENTOS A LONGO PRAZO - INTERNO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 368,'2013','2.2.2.3.5.00.00','FINANCIAMENTOS A LONGO PRAZO - INTERNO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 369,'2013','2.2.2.4.0.00.00','FINANCIAMENTO A LONGO PRAZO - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 370,'2013','2.2.2.4.1.00.00','FINANCIAMENTO A LONGO PRAZO - EXTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 371,'2013','2.2.2.5.0.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 372,'2013','2.2.2.5.1.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 373,'2013','2.2.2.5.3.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - INTERNO -INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 374,'2013','2.2.2.5.4.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - INTERNO -INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 375,'2013','2.2.2.5.5.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - INTERNO -INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 376,'2013','2.2.2.6.0.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 377,'2013','2.2.2.6.1.00.00','JUROS E ENCARGOS A PAGAR DE EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO - EXTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 378,'2013','2.2.2.8.0.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - INTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 379,'2013','2.2.2.8.1.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - INTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 380,'2013','2.2.2.8.3.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - INTERNO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 381,'2013','2.2.2.8.4.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - INTERNO - INTER OFFS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 382,'2013','2.2.2.8.5.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - INTERNO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 383,'2013','2.2.2.9.0.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - EXTERNO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 384,'2013','2.2.2.9.1.00.00','(-) ENCARGOS FINANCEIROS A APROPRIAR - EXTERNO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 385,'2013','2.2.3.0.0.00.00','FORNECEDORES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 386,'2013','2.2.3.1.0.00.00','FORNECEDORES NACIONAIS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 387,'2013','2.2.3.1.1.00.00','FORNECEDORES NACIONAIS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 388,'2013','2.2.3.2.0.00.00','FORNECEDORES ESTRANGEIROS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 389,'2013','2.2.3.2.1.00.00','FORNECEDORES ESTRANGEIROS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 390,'2013','2.2.4.0.0.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 391,'2013','2.2.4.1.0.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM A UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 392,'2013','2.2.4.1.1.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM A UNIÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 393,'2013','2.2.4.1.2.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM A UNIÃO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 394,'2013','2.2.4.1.3.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM A UNIÃO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 395,'2013','2.2.4.1.4.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM A UNIÃO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 396,'2013','2.2.4.1.5.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM A UNIÃO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 397,'2013','2.2.4.2.0.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM OS ESTADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 398,'2013','2.2.4.2.1.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM OS ESTADOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 399,'2013','2.2.4.2.2.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM OS ESTADOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 400,'2013','2.2.4.3.0.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM OS MUNICÍPIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 401,'2013','2.2.4.3.1.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM OS MUNICÍPIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 402,'2013','2.2.4.3.2.00.00','OBRIGAÇÕES FISCAIS A LONGO PRAZO COM OS MUNICÍPIOS-INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 403,'2013','2.2.7.0.0.00.00','PROVISÕES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 404,'2013','2.2.7.1.0.00.00','PROVISÃO PARA RISCOS TRABALHISTAS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 405,'2013','2.2.7.1.1.00.00','PROVISÃO PARA RISCOS TRABALHISTAS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 406,'2013','2.2.7.2.0.00.00','PROVISÕES MATEMÁTICAS PREVIDÊNCIÁRIAS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 407,'2013','2.2.7.2.1.00.00','PROVISÕES MATEMÁTICAS PREVIDÊNCIÁRIAS A LONGO PRAZO - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 408,'2013','2.2.7.2.1.01.00','PLANO FINANCEIRO - PROVISOES DE BENEFICIOS CONCEDIDOS   ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 409,'2013','2.2.7.2.1.02.00','PLANO FINANCEIRO - PROVISOES DE BENEFICIOS A CONCEDER            ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 410,'2013','2.2.7.2.1.03.00','PLANO PREVIDENCIARIO - PROVISOES DE BENEFICIOS CONCEDIDOS            ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 411,'2013','2.2.7.2.1.04.00','PLANO PREVIDENCIARIO - PROVISOES DE BENEFICIOS A CONCEDER              ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 412,'2013','2.2.7.2.1.05.00',' PLANO PREVIDENCIARIO - PLANO DE AMORTIZACAO                            ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 413,'2013','2.2.7.2.1.06.00',' PROVISOES ATUARIAIS PARA AJUSTES DO PLANO FINANCEIRO      ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 414,'2013','2.2.7.2.1.07.00',' PROVISOES ATUARIAIS PARA AJUSTES DO PLANO PREVIDENCIARIO      ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 415,'2013','2.2.7.3.0.00.00','PROVISÃO PARA RISCOS FISCAIS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 416,'2013','2.2.7.3.1.00.00','PROVISÃO PARA RISCOS FISCAIS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 417,'2013','2.2.7.4.0.00.00','PROVISÃO PARA RISCOS CÍVEIS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 418,'2013','2.2.7.4.1.00.00','PROVISÃO PARA RISCOS CÍVEIS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 419,'2013','2.2.7.5.0.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 420,'2013','2.2.7.5.3.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A LONGO PRAZO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 421,'2013','2.2.7.5.4.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A LONGO PRAZO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 422,'2013','2.2.7.5.5.00.00','PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS A LONGO PRAZO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 423,'2013','2.2.7.6.0.00.00','PROVISÃO PARA RISCOS DECORRENTES DE CONTRATOS DE PPP A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 424,'2013','2.2.7.6.1.00.00','PROVISÃO PARA RISCOS DECORRENTES DE CONTRATOS DE PPP A LONGO PRAZO - CONSOLIDAÇÃO OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 425,'2013','2.2.7.9.0.00.00','OUTRAS PROVISÕES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 426,'2013','2.2.7.9.1.00.00','OUTRAS PROVISÕES A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 427,'2013','2.2.8.0.0.00.00','DEMAIS OBRIGAÇÕES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 428,'2013','2.2.8.1.0.00.00','ADIANTAMENTOS DE CLIENTES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 429,'2013','2.2.8.1.1.00.00','ADIANTAMENTOS DE CLIENTES A LONGO PRAZO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 430,'2013','2.2.8.2.0.00.00','OBRIGAÇÕES POR DANOS A TERCEIROS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 431,'2013','2.2.8.2.1.00.00','OBRIGAÇÕES POR DANOS A TERCEIROS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 432,'2013','2.2.8.3.0.00.00','DEBÊNTURES E OUTROS TÍTULOS DE DIVIDA A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 433,'2013','2.2.8.3.1.00.00','DEBÊNTURES E OUTROS TÍTULOS DE DIVIDA A LONGO PRAZO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 434,'2013','2.2.8.4.0.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 435,'2013','2.2.8.4.1.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 436,'2013','2.2.8.9.0.00.00','OUTRAS OBRIGAÇÕES A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 437,'2013','2.2.8.9.1.00.00','OUTRAS OBRIGAÇÕES A LONGO PRAZO- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 438,'2013','2.2.9.0.0.00.00','RESULTADO DIFERIDO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 439,'2013','2.2.9.1.0.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA (VPA) DIFERIDA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 440,'2013','2.2.9.1.1.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA DIFERIDA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 441,'2013','2.2.9.2.0.00.00','(-) CUSTO DIFERIDO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 442,'2013','2.2.9.2.1.00.00','(-) CUSTO DIFERIDO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 443,'2013','2.3.0.0.0.00.00','PATRIMÔNIO LIQUIDO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 444,'2013','2.3.1.0.0.00.00','PATRIMÔNIO SOCIAL E CAPITAL SOCIAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 445,'2013','2.3.1.1.0.00.00','PATRIMÔNIO SOCIAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 446,'2013','2.3.1.1.1.00.00','PATRIMÔNIO SOCIAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 447,'2013','2.3.1.2.0.00.00','CAPITAL SOCIAL REALIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 448,'2013','2.3.1.2.1.00.00','CAPITAL SOCIAL REALIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 449,'2013','2.3.1.2.2.00.00','CAPITAL SOCIAL REALIZADO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 450,'2013','2.3.1.2.3.00.00','CAPITAL SOCIAL REALIZADO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 451,'2013','2.3.1.2.4.00.00','CAPITAL SOCIAL REALIZADO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 452,'2013','2.3.1.2.5.00.00','CAPITAL SOCIAL REALIZADO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 453,'2013','2.3.2.0.0.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 454,'2013','2.3.2.0.1.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 455,'2013','2.3.2.0.2.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 456,'2013','2.3.2.0.3.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 457,'2013','2.3.2.0.4.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 458,'2013','2.3.2.0.5.00.00','ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL - INTER OFSS - MUNICÍPIO.','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 459,'2013','2.3.3.0.0.00.00','RESERVAS DE CAPITAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 460,'2013','2.3.3.1.0.00.00','ÁGIO NA EMISSÃO DE AÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 461,'2013','2.3.3.1.1.00.00','ÁGIO NA EMISSÃO DE AÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 462,'2013','2.3.3.1.2.00.00','ÁGIO NA EMISSÃO DE AÇÕES - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 463,'2013','2.3.3.1.3.00.00','ÁGIO NA EMISSÃO DE AÇÕES - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 464,'2013','2.3.3.1.4.00.00','ÁGIO NA EMISSÃO DE AÇÕES - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 465,'2013','2.3.3.1.5.00.00','ÁGIO NA EMISSÃO DE AÇÕES - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 466,'2013','2.3.3.2.0.00.00','ALIENAÇÃO DE PARTES BENEFICIARIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 467,'2013','2.3.3.2.1.00.00','ALIENAÇÃO DE PARTES BENEFICIARIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 468,'2013','2.3.3.2.2.00.00','ALIENAÇÃO DE PARTES BENEFICIARIAS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 469,'2013','2.3.3.2.3.00.00','ALIENAÇÃO DE PARTES BENEFICIARIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 470,'2013','2.3.3.2.4.00.00','ALIENAÇÃO DE PARTES BENEFICIARIAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 471,'2013','2.3.3.2.5.00.00','ALIENAÇÃO DE PARTES BENEFICIARIAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 472,'2013','2.3.3.3.0.00.00','ALIENAÇÃO DE BÔNUS DE SUBSCRIÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 473,'2013','2.3.3.3.1.00.00','ALIENAÇÃO DE BÔNUS DE SUBSCRIÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 474,'2013','2.3.3.3.2.00.00','ALIENAÇÃO DE BÔNUS DE SUBSCRIÇÃO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 475,'2013','2.3.3.3.3.00.00','ALIENAÇÃO DE BÔNUS DE SUBSCRIÇÃO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 476,'2013','2.3.3.3.4.00.00','ALIENAÇÃO DE BÔNUS DE SUBSCRIÇÃO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 477,'2013','2.3.3.3.5.00.00','ALIENAÇÃO DE BÔNUS DE SUBSCRIÇÃO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 478,'2013','2.3.3.4.0.00.00','CORREÇÃO MONETÁRIA DO CAPITAL REALIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 479,'2013','2.3.3.4.1.00.00','CORREÇÃO MONETÁRIA DO CAPITAL REALIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 480,'2013','2.3.3.4.2.00.00','CORREÇÃO MONETÁRIA DO CAPITAL REALIZADO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 481,'2013','2.3.3.4.3.00.00','CORREÇÃO MONETÁRIA DO CAPITAL REALIZADO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 482,'2013','2.3.3.4.4.00.00','CORREÇÃO MONETÁRIA DO CAPITAL REALIZADO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 483,'2013','2.3.3.4.5.00.00','CORREÇÃO MONETÁRIA DO CAPITAL REALIZADO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 484,'2013','2.3.3.9.0.00.00','OUTRAS RESERVAS DE CAPITAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 485,'2013','2.3.3.9.1.00.00','OUTRAS RESERVAS DE CAPITAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 486,'2013','2.3.3.9.2.00.00','OUTRAS RESERVAS DE CAPITAL - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 487,'2013','2.3.3.9.3.00.00','OUTRAS RESERVAS DE CAPITAL - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 488,'2013','2.3.3.9.4.00.00','OUTRAS RESERVAS DE CAPITAL - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 489,'2013','2.3.3.9.5.00.00','OUTRAS RESERVAS DE CAPITAL - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 490,'2013','2.3.4.0.0.00.00','AJUSTES DE AVALIAÇÃO PATRIMONIAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 491,'2013','2.3.4.1.0.00.00','AJUSTES DE AVALIAÇÃO PATRIMONIAL DE ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 492,'2013','2.3.4.1.1.00.00','AJUSTES DE AVALIAÇÃO PATRIMONIAL DE ATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 493,'2013','2.3.4.2.0.00.00','AJUSTES DE AVALIAÇÃO PATRIMONIAL DE PASSIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 494,'2013','2.3.4.2.1.00.00','AJUSTES DE AVALIAÇÃO PATRIMONIAL DE PASSIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 495,'2013','2.3.5.0.0.00.00','RESERVAS DE LUCROS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 496,'2013','2.3.5.1.0.00.00','RESERVA LEGAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 497,'2013','2.3.5.1.1.00.00','RESERVA LEGAL- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 498,'2013','2.3.5.1.2.00.00','RESERVA LEGAL- INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 499,'2013','2.3.5.1.3.00.00','RESERVA LEGAL- INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 500,'2013','2.3.5.1.4.00.00','RESERVA LEGAL- INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 501,'2013','2.3.5.1.5.00.00','RESERVA LEGAL- INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 502,'2013','2.3.5.2.0.00.00','RESERVAS ESTATUTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 503,'2013','2.3.5.2.1.00.00','RESERVAS ESTATUTÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 504,'2013','2.3.5.2.2.00.00','RESERVAS ESTATUTÁRIAS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 505,'2013','2.3.5.2.3.00.00','RESERVAS ESTATUTÁRIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 506,'2013','2.3.5.2.4.00.00','RESERVAS ESTATUTÁRIAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 507,'2013','2.3.5.2.5.00.00','RESERVAS ESTATUTÁRIAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 508,'2013','2.3.5.3.0.00.00','RESERVA PARA CONTINGENCIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 509,'2013','2.3.5.3.1.00.00','RESERVA PARA CONTINGENCIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 510,'2013','2.3.5.3.2.00.00','RESERVA PARA CONTINGENCIAS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 511,'2013','2.3.5.3.3.00.00','RESERVA PARA CONTINGENCIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 512,'2013','2.3.5.3.4.00.00','RESERVA PARA CONTINGENCIAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 513,'2013','2.3.5.3.5.00.00','RESERVA PARA CONTINGENCIAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 514,'2013','2.3.5.4.0.00.00','RESERVA DE INCENTIVOS FISCAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 515,'2013','2.3.5.4.1.00.00','RESERVA DE INCENTIVOS FISCAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 516,'2013','2.3.5.4.2.00.00','RESERVA DE INCENTIVOS FISCAIS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 517,'2013','2.3.5.4.3.00.00','RESERVA DE INCENTIVOS FISCAIS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 518,'2013','2.3.5.4.4.00.00','RESERVA DE INCENTIVOS FISCAIS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 519,'2013','2.3.5.4.5.00.00','RESERVA DE INCENTIVOS FISCAIS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 520,'2013','2.3.5.5.0.00.00','RESERVAS DE LUCROS PARA EXPANSÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 521,'2013','2.3.5.5.1.00.00','RESERVAS DE LUCROS PARA EXPANSÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 522,'2013','2.3.5.5.2.00.00','RESERVAS DE LUCROS PARA EXPANSÃO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 523,'2013','2.3.5.5.3.00.00','RESERVAS DE LUCROS PARA EXPANSÃO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 524,'2013','2.3.5.5.4.00.00','RESERVAS DE LUCROS PARA EXPANSÃO - INTER OFSS -ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 525,'2013','2.3.5.5.5.00.00','RESERVAS DE LUCROS PARA EXPANSÃO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 526,'2013','2.3.5.6.0.00.00','RESERVA DE LUCROS A REALIZAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 527,'2013','2.3.5.6.1.00.00','RESERVA DE LUCROS A REALIZAR- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 528,'2013','2.3.5.6.2.00.00','RESERVA DE LUCROS A REALIZAR- INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 529,'2013','2.3.5.6.3.00.00','RESERVA DE LUCROS A REALIZAR- INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 530,'2013','2.3.5.6.4.00.00','RESERVA DE LUCROS A REALIZAR- INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 531,'2013','2.3.5.6.5.00.00','RESERVA DE LUCROS A REALIZAR- INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 532,'2013','2.3.5.7.0.00.00','RESERVA DE RETENÇÃO DE PREMIO NA EMISSÃO DE DEBÊNTURES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 533,'2013','2.3.5.7.1.00.00','RESERVA DE RETENÇÃO DE PREMIO NA EMISSÃO DE DEBÊNTURES- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 534,'2013','2.3.5.7.2.00.00','RESERVA DE RETENÇÃO DE PREMIO NA EMISSÃO DE DEBÊNTURES- INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 535,'2013','2.3.5.7.3.00.00','RESERVA DE RETENÇÃO DE PREMIO NA EMISSÃO DE DEBÊNTURES- INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 536,'2013','2.3.5.7.4.00.00','RESERVA DE RETENÇÃO DE PREMIO NA EMISSÃO DE DEBÊNTURES- INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 537,'2013','2.3.5.7.5.00.00','RESERVA DE RETENÇÃO DE PREMIO NA EMISSÃO DE DEBÊNTURES- INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 538,'2013','2.3.5.9.0.00.00','OUTRAS RESERVAS DE LUCRO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 539,'2013','2.3.5.9.1.00.00','OUTRAS RESERVAS DE LUCRO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 540,'2013','2.3.5.9.2.00.00','OUTRAS RESERVAS DE LUCRO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 541,'2013','2.3.5.9.3.00.00','OUTRAS RESERVAS DE LUCRO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 542,'2013','2.3.5.9.4.00.00','OUTRAS RESERVAS DE LUCRO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 543,'2013','2.3.5.9.5.00.00','OUTRAS RESERVAS DE LUCRO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 544,'2013','2.3.6.0.0.00.00','DEMAIS RESERVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 545,'2013','2.3.6.1.0.00.00','RESERVA DE REAVALIAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 546,'2013','2.3.6.1.1.00.00','RESERVA DE REAVALIAÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 547,'2013','2.3.6.1.2.00.00','RESERVA DE REAVALIAÇÃO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 548,'2013','2.3.6.1.3.00.00','RESERVA DE REAVALIAÇÃO - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 549,'2013','2.3.6.1.4.00.00','RESERVA DE REAVALIAÇÃO - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 550,'2013','2.3.6.1.5.00.00','RESERVA DE REAVALIAÇÃO - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 551,'2013','2.3.6.9.0.00.00','OUTRAS RESERVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 552,'2013','2.3.6.9.1.00.00','OUTRAS RESERVAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 553,'2013','2.3.6.9.2.00.00','OUTRAS RESERVAS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 554,'2013','2.3.6.9.3.00.00','OUTRAS RESERVAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 555,'2013','2.3.6.9.4.00.00','OUTRAS RESERVAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 556,'2013','2.3.6.9.5.00.00','OUTRAS RESERVAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 557,'2013','2.3.7.0.0.00.00','RESULTADOS ACUMULADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 558,'2013','2.3.7.1.0.00.00','SUPERÁVITS OU DÉFICITS ACUMULADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 559,'2013','2.3.7.1.1.00.00','SUPERÁVITS OU DÉFICITS ACUMULADOS - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 560,'2013','2.3.7.1.1.01.00','SUPERÁVITS OU DÉFICITS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 561,'2013','2.3.7.1.1.02.00','SUPERAVITS OU DEFICITS DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 562,'2013','2.3.7.1.1.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 563,'2013','2.3.7.1.1.04.00','SUPERÁVITS OU DÉFICITS RESULTANTES DE EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 564,'2013','2.3.7.1.2.00.00','SUPERÁVITS OU DÉFICITS ACUMULADOS - INTRA OFSS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 565,'2013','2.3.7.1.2.01.00','SUPERÁVITS OU DÉFICITS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 566,'2013','2.3.7.1.2.02.00','SUPERAVITS OU DEFICITS DE EXERCICIOS ANTERIORES ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 567,'2013','2.3.7.1.2.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 568,'2013','2.3.7.1.2.04.00','SUPERÁVITS OU DÉFICITS RESULTANTES DE EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 569,'2013','2.3.7.1.3.00.00','SUPERÁVITS OU DÉFICITS ACUMULADOS - INTER OFSS - UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 570,'2013','2.3.7.1.3.01.00','SUPERÁVITS OU DÉFICITS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 571,'2013','2.3.7.1.3.02.00','SUPERAVITS OU DEFICITS DE EXERCICIOS ANTERIORES ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 572,'2013','2.3.7.1.3.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 573,'2013','2.3.7.1.3.04.00','SUPERÁVITS OU DÉFICITS RESULTANTES DE EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 574,'2013','2.3.7.1.4.00.00','SUPERÁVITS OU DÉFICITS ACUMULADOS - INTER OFSS - ESTADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 575,'2013','2.3.7.1.4.01.00','SUPERÁVITS OU DÉFICITS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 576,'2013','2.3.7.1.4.02.00','SUPERAVITS OU DEFICITS DE EXERCICIOS ANTERIORES ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 577,'2013','2.3.7.1.4.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 578,'2013','2.3.7.1.4.04.00','SUPERÁVITS OU DÉFICITS RESULTANTES DE EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 579,'2013','2.3.7.1.5.00.00','SUPERÁVITS OU DÉFICITS ACUMULADOS - INTER OFSS - MUNICÍPIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 580,'2013','2.3.7.1.5.01.00','SUPERÁVITS OU DÉFICITS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 581,'2013','2.3.7.1.5.02.00','SUPERAVITS OU DEFICITS DE EXERCICIOS ANTERIORES ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 582,'2013','2.3.7.1.5.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 583,'2013','2.3.7.1.5. 04.00','SUPERÁVITS OU DÉFICITS RESULTANTES DE EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 584,'2013','2.3.7.2.0.00.00','LUCROS E PREJUÍZOS ACUMULADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 585,'2013','2.3.7.2.1.00.00','LUCROS E PREJUÍZOS ACUMULADOS - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 586,'2013','2.3.7.2.1.01.00','LUCROS E PREJUÍZOS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 587,'2013','2.3.7.2.1.02.00','LUCROS E PREJUÍZOS ACUMULADOS DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 588,'2013','2.3.7.2.1.03.00','AJUSTES DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 589,'2013','2.3.7.2.1.04.00','LUCROS A DESTINAR DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 590,'2013','2.3.7.2.1.05.00','LUCROS A DESTINAR DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 591,'2013','2.3.7.2.1.06.00','RESULTADOS APURADOS POR EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 592,'2013','2.3.7.2.2.00.00','LUCROS E PREJUÍZOS ACUMULADOS - INTRA OFSS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 593,'2013','2.3.7.2.2.01.00','LUCROS E PREJUÍZOS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 594,'2013','2.3.7.2.2.02.00','LUCROS EPREJUIZOS ACUMULADOS DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 595,'2013','2.3.7.2.2.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 596,'2013','2.3.7.2.2.04.00','LUCROS A DESTINAR DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 597,'2013','2.3.7.2.2.05.00','LUCROS A DESTINAR DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 598,'2013','2.3.7.2.2.06.00','RESULTADOS APURADOS POR EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 599,'2013','2.3.7.2.3.00.00','LUCROS E PREJUÍZOS ACUMULADOS - INTER OFSS - UNIÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 600,'2013','2.3.7.2.3.01.00','LUCROS E PREJUÍZOS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 601,'2013','2.3.7.2.3.02.00','LUCROS E PREJUIZOS ACUMULADOS DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 602,'2013','2.3.7.2.3.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 603,'2013','2.3.7.2.3.04.00','LUCROS A DESTINAR DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 604,'2013','2.3.7.2.3.05.00','LUCROS A DESTINAR DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 605,'2013','2.3.7.2.3.06.00','RESULTADOS APURADOS POR EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 606,'2013','2.3.7.2.4.00.00','LUCROS E PREJUÍZOS ACUMULADOS - INTER OFSS - ESTADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 607,'2013','2.3.7.2.4.01.00','LUCROS E PREJUÍZOS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 608,'2013','2.3.7.2.4.02.00','LUCROS E PREJUIZOS ACUMULADOS DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 609,'2013','2.3.7.2.4.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 610,'2013','2.3.7.2.4.04.00','LUCROS A DESTINAR DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 611,'2013','2.3.7.2.4.05.00','LUCROS A DESTINAR DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 612,'2013','2.3.7.2.4.06.00','RESULTADOS APURADOS POR EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 613,'2013','2.3.7.2.5.00.00','LUCROS E PREJUÍZOS ACUMULADOS - INTER OFSS - MUNICÍPIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 614,'2013','2.3.7.2.5.01.00','LUCROS E PREJUÍZOS DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 615,'2013','2.3.7.2.5.02.00','LUCROS E PREJUIZOS ACUMULADOS DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 616,'2013','2.3.7.2.5.03.00','AJUSTES DE EXERCICIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 617,'2013','2.3.7.2.5. 04.00','LUCROS A DESTINAR DO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 618,'2013','2.3.7.2.5. 05.00','LUCROS A DESTINAR DE EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 619,'2013','2.3.7.2.5. 06.00','RESULTADOS APURADOS POR EXTINÇÃO, FUSÃO E CISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 620,'2013','2.3.9.0.0.00.00','(-) AÇÕES / COTAS EM TESOURARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 621,'2013','2.3.9.1.0.00.00','(-) AÇÕES EM TESOURARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 622,'2013','2.3.9.1.1.00.00','(-) AÇÕES EM TESOURARIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 623,'2013','2.3.9.1.2.00.00','(-) AÇÕES EM TESOURARIA - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 624,'2013','2.3.9.1.3.00.00','(-) AÇÕES EM TESOURARIA - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 625,'2013','2.3.9.1.4.00.00','(-) AÇÕES EM TESOURARIA - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 626,'2013','2.3.9.1.5.00.00','(-) AÇÕES EM TESOURARIA - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 627,'2013','2.3.9.2.0.00.00','(-) COTAS EM TESOURARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 628,'2013','2.3.9.2.1.00.00','(-) COTAS EM TESOURARIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 629,'2013','2.3.9.2.2.00.00','(-) COTAS EM TESOURARIA - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 630,'2013','2.3.9.2.3.00.00','(-) COTAS EM TESOURARIA - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 631,'2013','2.3.9.2.4.00.00','(-) COTAS EM TESOURARIA - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 632,'2013','2.3.9.2.5.00.00','(-) COTAS EM TESOURARIA - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 633,'2013','3.0.0.0.0.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 634,'2013','3.1.0.0.0.00.00','PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 635,'2013','3.1.1.0.0.00.00','REMUNERAÇÃO A PESSOAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 636,'2013','3.1.1.1.0.00.00','REMUNERAÇÃO A PESSOAL ATIVO CIVIL - ABRANGIDOS PELO RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 637,'2013','3.1.1.1.1.00.00','REMUNERAÇÃO A PESSOAL ATIVO CIVIL - ABRANGIDOS PELO RPPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 638,'2013','3.1.1.2.0.00.00','REMUNERAÇÃO A PESSOAL ATIVO CIVIL - ABRANGIDOS PELO   RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 639,'2013','3.1.1.2.1.00.00','REMUNERAÇÃO A PESSOAL ATIVO CIVIL - ABRANGIDOS PELO RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 640,'2013','3.1.1.3.0.00.00','REMUNERAÇÃO A PESSOAL ATIVO MILITAR - ABRANGIDOS PELO REGIME PRÓPRIO DOS MILITARES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 641,'2013','3.1.1.3.1.00.00','REMUNERAÇÃO A PESSOAL ATIVO MILITAR - ABRANGIDOS PELO REGIME PRÓPRIO DOS MILITARES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 642,'2013','3.1.2.0.0.00.00','ENCARGOS PATRONAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 643,'2013','3.1.2.1.0.00.00','ENCARGOS PATRONAIS - RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 644,'2013','3.1.2.1.2.00.00','ENCARGOS PATRONAIS - RPPS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 645,'2013','3.1.2.2.0.00.00','ENCARGOS PATRONAIS - RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 646,'2013','3.1.2.2.1.00.00','ENCARGOS PATRONAIS - RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 647,'2013','3.1.2.2.2.00.00','ENCARGOS PATRONAIS - RGPS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 648,'2013','3.1.2.2.3.00.00','ENCARGOS PATRONAIS - RGPS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 649,'2013','3.1.2.2.4.00.00','ENCARGOS PATRONAIS - RGPS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 650,'2013','3.1.2.2.5.00.00','ENCARGOS PATRONAIS - RGPS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 651,'2013','3.1.2.3.0.00.00','ENCARGOS PATRONAIS - FGTS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 652,'2013','3.1.2.3.1.00.00','ENCARGOS PATRONAIS - FGTS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 653,'2013','3.1.2.4.0.00.00','CONTRIBUIÇÕES SOCIAIS GERAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 654,'2013','3.1.2.4.1.00.00','CONTRIBUIÇÕES SOCIAIS GERAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 655,'2013','3.1.2.5.0.00.00','CONTRIBUIÇÕES A ENTIDADES FECHADAS DE PREVIDÊNCIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 656,'2013','3.1.2.5.1.00.00','CONTRIBUIÇÕES A ENTIDADES FECHADAS DE PREVIDÊNCIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 657,'2013','3.1.2.9.0.00.00','OUTROS ENCARGOS PATRONAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 658,'2013','3.1.2.9.1.00.00','OUTROS ENCARGOS PATRONAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 659,'2013','3.1.2.9.2.00.00','OUTROS ENCARGOS PATRONAIS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 660,'2013','3.1.2.9.3.00.00','OUTROS ENCARGOS PATRONAIS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 661,'2013','3.1.2.9.4.00.00','OUTROS ENCARGOS PATRONAIS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 662,'2013','3.1.2.9.5.00.00','OUTROS ENCARGOS PATRONAIS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 663,'2013','3.1.3.0.0.00.00','BENEFÍCIOS A PESSOAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 664,'2013','3.1.3.1.0.00.00','BENEFÍCIOS A PESSOAL - RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 665,'2013','3.1.3.1.1.00.00','BENEFÍCIOS A PESSOAL - RPPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 666,'2013','3.1.3.2.0.00.00','BENEFÍCIOS A PESSOAL - RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 667,'2013','3.1.3.2.1.00.00','BENEFÍCIOS A PESSOAL - RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 668,'2013','3.1.3.3.0.00.00','BENEFÍCIOS A PESSOAL - MILITAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 669,'2013','3.1.3.3.1.00.00','BENEFÍCIOS A PESSOAL - MILITAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 670,'2013','3.1.8.0.0.00.00','CUSTO DE PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 671,'2013','3.1.8.1.0.00.00','CUSTO DE MERCADORIAS VENDIDAS - PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 672,'2013','3.1.8.1.1.00.00','CUSTO DE MERCADORIAS VENDIDAS - PESSOAL E ENCARGOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 673,'2013','3.1.8.2.0.00.00','CUSTO DE PRODUTOS VENDIDOS - PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 674,'2013','3.1.8.2.1.00.00','CUSTO DE PRODUTOS VENDIDOS - PESSOAL E ENCARGOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 675,'2013','3.1.8.3.0.00.00','CUSTO DE SERVIÇOS PRESTADOS - PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 676,'2013','3.1.8.3.1.00.00','CUSTO DE SERVIÇOS PRESTADOS - PESSOAL E ENCARGOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 677,'2013','3.1.9.0.0.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS - PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 678,'2013','3.1.9.1.0.00.00','INDENIZAÇÕES E RESTITUIÇÕES TRABALHISTAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 679,'2013','3.1.9.1.1.00.00','INDENIZAÇÕES E RESTITUIÇÕES TRABALHISTAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 680,'2013','3.1.9.2.0.00.00','PESSOAL REQUISITADO DE OUTROS ORGAOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 681,'2013','3.1.9.2.1.00.00','PESSOAL REQUISITADO DE OUTROS ORGAOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 682,'2013','3.1.9.9.0.00.00','OUTRAS VPD DE PESSOAL E ENCARGOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 683,'2013','3.1.9.9.1.00.00','OUTRAS VPD DE PESSOAL E ENCARGOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 684,'2013','3.2.0.0.0.00.00','BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 685,'2013','3.2.1.0.0.00.00','APOSENTADORIAS E REFORMAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 686,'2013','3.2.1.1.0.00.00','APOSENTADORIAS - RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 687,'2013','3.2.1.1.1.00.00','APOSENTADORIAS - RPPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 688,'2013','3.2.1.2.0.00.00','APOSENTADORIAS - RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 689,'2013','3.2.1.2.1.00.00','APOSENTADORIAS - RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 690,'2013','3.2.1.3.0.00.00','RESERVA REMUNERADA E REFORMAS - MILITAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 691,'2013','3.2.1.3.1.00.00','RESERVA REMUNERADA E REFORMAS - MILITAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 692,'2013','3.2.1.9.0.00.00','OUTRAS APOSENTADORIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 693,'2013','3.2.1.9.1.00.00','OUTRAS APOSENTADORIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 694,'2013','3.2.2.0.0.00.00','PENSÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 695,'2013','3.2.2.1.0.00.00','PENSÕES - RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 696,'2013','3.2.2.1.1.00.00','PENSÕES - RPPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 697,'2013','3.2.2.2.0.00.00','PENSÕES - RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 698,'2013','3.2.2.2.1.00.00','PENSÕES - RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 699,'2013','3.2.2.3.0.00.00','PENSÕES - MILITAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 700,'2013','3.2.2.3.1.00.00','PENSÕES - MILITAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 701,'2013','3.2.2.9.0.00.00','OUTRAS PENSÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 702,'2013','3.2.2.9.1.00.00','OUTRAS PENSÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 703,'2013','3.2.3.0.0.00.00','BENEFÍCIOS DE PRESTAÇÃO CONTINUADA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 704,'2013','3.2.3.1.0.00.00','BENEFÍCIOS DE PRESTAÇÃO CONTINUADA AO IDOSO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 705,'2013','3.2.3.1.1.00.00','BENEFÍCIOS DE PRESTAÇÃO CONTINUADA AO IDOSO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 706,'2013','3.2.3.2.0.00.00','BENEFÍCIOS DE PRESTAÇÃO CONTINUADA AO PORTADOR DE DEFICIÊNCIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 707,'2013','3.2.3.2.1.00.00','BENEFÍCIOS DE PRESTAÇÃO CONTINUADA AO PORTADOR DE DEFICIÊNCIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 708,'2013','3.2.3.9.0.00.00','OUTROS BENEFÍCIOS DE PRESTAÇÃO CONTINUADA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 709,'2013','3.2.3.9.1.00.00','OUTROS BENEFÍCIOS DE PRESTAÇÃO CONTINUADA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 710,'2013','3.2.4.0.0.00.00','BENEFÍCIOS EVENTUAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 711,'2013','3.2.4.1.0.00.00','AUXÍLIO POR NATALIDADE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 712,'2013','3.2.4.1.1.00.00','AUXÍLIO POR NATALIDADE - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 713,'2013','3.2.4.2.0.00.00','AUXÍLIO POR MORTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 714,'2013','3.2.4.2.1.00.00','AUXÍLIO POR MORTE - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 715,'2013','3.2.4.3.0.00.00','BENEFÍCIOS EVENTUAIS POR SITUAÇÕES DE VULNERABILIDADE TEMPORÁRIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 716,'2013','3.2.4.3.1.00.00','BENEFÍCIOS EVENTUAIS POR SITUAÇÕES DE VULNERABILIDADE TEMPORÁRIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 717,'2013','3.2.4.4.0.00.00','BENEFÍCIOS EVENTUAIS EM CASO DE CALAMIDADE PÚBLICA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 718,'2013','3.2.4.4.1.00.00','BENEFÍCIOS EVENTUAIS EM CASO DE CALAMIDADE PÚBLICA - COSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 719,'2013','3.2.4.9.0.00.00','OUTROS BENEFÍCIOS EVENTUAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 720,'2013','3.2.4.9.1.00.00','OUTROS BENEFÍCIOS EVENTUAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 721,'2013','3.2.5.0.0.00.00','POLÍTICAS PÚBLICAS DE TRANSFERÊNCIA DE RENDA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 722,'2013','3.2.5.0.1.00.00','POLÍTICAS PÚBLICAS DE TRANSFERÊNCIA DE RENDA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 723,'2013','3.2.9.0.0.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 724,'2013','3.2.9.1.0.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS - RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 725,'2013','3.2.9.1.1.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS - RPPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 726,'2013','3.2.9.2.0.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS - RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 727,'2013','3.2.9.2.1.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS - RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 728,'2013','3.2.9.3.0.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS - MILITAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 729,'2013','3.2.9.3.1.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS - MILITAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 730,'2013','3.2.9.9.0.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 731,'2013','3.2.9.9.1.00.00','OUTROS BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 732,'2013','3.3.0.0.0.00.00','USO DE BENS, SERVIÇOS E CONSUMO DE CAPITAL FIXO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 733,'2013','3.3.1.0.0.00.00','USO DE MATERIAL DE CONSUMO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 734,'2013','3.3.1.1.0.00.00','CONSUMO DE MATERIAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 735,'2013','3.3.1.1.1.00.00','CONSUMO DE MATERIAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 736,'2013','3.3.1.2.0.00.00','DISTRIBUIÇÃO DE MATERIAL GRATUITO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 737,'2013','3.3.1.2.1.00.00','DISTRIBUIÇÃO DE MATERIAL GRATUITO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 738,'2013','3.3.2.0.0.00.00','SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 739,'2013','3.3.2.1.0.00.00','DIÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 740,'2013','3.3.2.1.1.00.00','DIÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 741,'2013','3.3.2.2.0.00.00','SERVIÇOS TERCEIROS - PF','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 742,'2013','3.3.2.2.1.00.00','SERVIÇOS TERCEIROS - PF - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 743,'2013','3.3.2.3.0.00.00','SERVIÇOS TERCEIROS - PJ','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 744,'2013','3.3.2.3.1.00.00','SERVIÇOS TERCEIROS - PJ - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 745,'2013','3.3.2.4.0.00.00','CONTRATO DE TERCEIRIZAÇÃO POR SUBSTITUIÇÃO DE MÃO DE OBRA - ART. 18 § 1, LC 101/00','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 746,'2013','3.3.2.4.1.00.00','CONTRATO DE TERCEIRIZAÇÃO POR SUBSTITUIÇÃO DE MÃO DE OBRA - ART. 18 § 1, LC 101/00 - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 747,'2013','3.3.3.0.0.00.00','DEPRECIAÇÃO, AMORTIZAÇÃO E EXAUSTÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 748,'2013','3.3.3.1.0.00.00','DEPRECIAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 749,'2013','3.3.3.1.1.00.00','DEPRECIAÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 750,'2013','3.3.3.2.0.00.00','AMORTIZAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 751,'2013','3.3.3.2.1.00.00','AMORTIZAÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 752,'2013','3.3.3.3.0.00.00','EXAUSTÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 753,'2013','3.3.3.3.1.00.00','EXAUSTÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 754,'2013','3.3.8.0.0.00.00','CUSTO DE MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 755,'2013','3.3.8.1.0.00.00','CUSTO DE MERCADORIAS VENDIDAS - MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 756,'2013','3.3.8.1.1.00.00','CUSTO DE MERCADORIAS VENDIDAS - MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 757,'2013','3.3.8.2.0.00.00','CUSTO DE PRODUTOS VENDIDOS - MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 758,'2013','3.3.8.2.1.00.00','CUSTO DE PRODUTOS VENDIDOS - MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 759,'2013','3.3.8.3.0.00.00','CUSTO DE SERVIÇOS PRESTADOS - MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 760,'2013','3.3.8.3.1.00.00','CUSTO DE SERVIÇOS PRESTADOS - MATÉRIAIS, SERVIÇOS E CONSUMO DE CAPITAL FIXO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 761,'2013','3.4.0.0.0.00.00','VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 762,'2013','3.4.1.0.0.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS OBTIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 763,'2013','3.4.1.1.0.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL INTERNA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 764,'2013','3.4.1.1.1.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL INTERNA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 765,'2013','3.4.1.1.3.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL INTERNA - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 766,'2013','3.4.1.1.4.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL INTERNA - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 767,'2013','3.4.1.1.5.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL INTERNA - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 768,'2013','3.4.1.2.0.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL EXTERNA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 769,'2013','3.4.1.2.1.00.00','JUROS E ENCARGOS DA DIVIDA CONTRATUAL EXTERNA - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 770,'2013','3.4.1.3.0.00.00','JUROS E ENCARGOS DA DIVIDA MOBILIARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 771,'2013','3.4.1.3.1.00.00','JUROS E ENCARGOS DA DIVIDA MOBILIARIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 772,'2013','3.4.1.4.0.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS POR ANTECIPAÇÃO DE RECEITA ORÇAMENTÁRIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 773,'2013','3.4.1.4.1.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS POR ANTECIPAÇÃO DE RECEITA ORÇAMENTÁRIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 774,'2013','3.4.1.8.0.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 775,'2013','3.4.1.8.1.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 776,'2013','3.4.1.8.3.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 777,'2013','3.4.1.8.4.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 778,'2013','3.4.1.8.5.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 779,'2013','3.4.1.9.0.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS EXTERNOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 780,'2013','3.4.1.9.1.00.00','OUTROS JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS EXTERNOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 781,'2013','3.4.2.0.0.00.00','JUROS E ENCARGOS DE MORA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 782,'2013','3.4.2.1.0.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS OBTIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 783,'2013','3.4.2.1.1.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS OBTIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 784,'2013','3.4.2.1.3.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS OBTIDOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 785,'2013','3.4.2.1.4.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS OBTIDOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 786,'2013','3.4.2.1.5.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS OBTIDOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 787,'2013','3.4.2.2.0.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS EXTERNOS OBTIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 788,'2013','3.4.2.2.1.00.00','JUROS E ENCARGOS DE MORA DE EMPRÉSTIMOS E FINANCIAMENTOS EXTERNOS OBTIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 789,'2013','3.4.2.3.0.00.00','JUROS E ENCARGOS DE MORA DE AQUISIÇÃO DE BENS E SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 790,'2013','3.4.2.3.1.00.00','JUROS E ENCARGOS DE MORA DE AQUISIÇÃO DE BENS E SERVIÇOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 791,'2013','3.4.2.4.0.00.00','JUROS E ENCARGOS DE MORA DE OBRIGAÇÕES TRIBUTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 792,'2013','3.4.2.4.1.00.00','JUROS E ENCARGOS DE MORA DE OBRIGAÇÕES TRIBUTÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 793,'2013','3.4.2.9.0.00.00','OUTROS JUROS E ENCARGOS DE MORA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 794,'2013','3.4.2.9.1.00.00','OUTROS JUROS E ENCARGOS DE MORA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 795,'2013','3.4.3.0.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 796,'2013','3.4.3.1.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL INTERNA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 797,'2013','3.4.3.1.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL INTERNA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 798,'2013','3.4.3.1.3.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL INTERNA - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 799,'2013','3.4.3.1.4.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL INTERNA - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 800,'2013','3.4.3.1.5.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL INTERNA - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 801,'2013','3.4.3.2.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL EXTERNA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 802,'2013','3.4.3.2.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA CONTRATUAL EXTERNA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 803,'2013','3.4.3.3.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA MOBILIARIA INTERNA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 804,'2013','3.4.3.3.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA MOBILIARIA INTERNA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 805,'2013','3.4.3.4.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA MOBILIARIA EXTERNA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 806,'2013','3.4.3.4.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE DIVIDA MOBILIARIA EXTERNA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 807,'2013','3.4.3.9.0.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 808,'2013','3.4.3.9.1.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 809,'2013','3.4.3.9.3.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 810,'2013','3.4.3.9.4.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 811,'2013','3.4.3.9.5.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 812,'2013','3.4.4.0.0.00.00','DESCONTOS FINANCEIROS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 813,'2013','3.4.4.0.1.00.00','DESCONTOS FINANCEIROS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 814,'2013','3.4.9.0.0.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS - FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 815,'2013','3.4.9.1.0.00.00','JUROS E ENCARGOS EM SENTENÇAS JUDICIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 816,'2013','3.4.9.1.1.00.00','JUROS E ENCARGOS EM SENTENÇAS JUDICIAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 817,'2013','3.4.9.2.0.00.00','JUROS E ENCARGOS EM INDENIZAÇÕES E RESTITUIÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 818,'2013','3.4.9.2.1.00.00','JUROS E ENCARGOS EM INDENIZAÇÕES E RESTITUIÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 819,'2013','3.4.9.9.0.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 820,'2013','3.4.9.9.1.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 821,'2013','3.5.0.0.0.00.00','TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 822,'2013','3.5.1.0.0.00.00','TRANSFERÊNCIAS INTRAGOVERNAMENTAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 823,'2013','3.5.1.1.0.00.00','TRANSFERENCIASCONCEDIDAS PARA A EXECUCAO ORCAMENTARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 824,'2013','3.5.1.1.2.00.00','TRANSFERENCIAS CONCEDIDAS PARA A EXECUÇÃO ORÇAMENTÁRIA  INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 825,'2013','3.5.1.2.0.00.00','TRANSFERENCIAS CONCEDIDAS - INDEPENDENTES DE EXECUCAO ORCAMENTARIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 826,'2013','3.5.1.2.2.00.00','TRANSFERENCIAS CONCEDIDAS  INDEPENDENTES DE EXECUCAO ORCAMENTARIA - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 827,'2013','3.5.1.3.0.00.00','TRANSFERENCIAS CONCEDIDAS PARA COBERTURA DO DÉFICIT ATUARIAL DO RPPS POR APORTE PERIÓDICO ','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 828,'2013','3.5.1.3.2.00.00','TRANSFERENCIAS CONCEDIDAS PARA COBERTURA DO DÉFICIT ATUARIAL DO RPPS POR APORTE PERIÓDICO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 829,'2013','3.5.2.0.0.00.00','TRANSFERÊNCIAS INTER GOVERNAMENTAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 830,'2013','3.5.2.1.0.00.00','DISTRIBUIÇÃO CONSTITUCIONAL OU LEGAL DE RECEITAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 831,'2013','3.5.2.1.1.00.00','DISTRIBUIÇÃO CONSTITUCIONAL OU LEGAL DE RECEITAS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 832,'2013','3.5.2.1.3.00.00','DISTRIBUIÇÃO CONSTITUCIONAL OU LEGAL DE RECEITAS- INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 833,'2013','3.5.2.1.4.00.00','DISTRIBUIÇÃO CONSTITUCIONAL OU LEGAL DE RECEITAS- INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 834,'2013','3.5.2.1.5.00.00','DISTRIBUIÇÃO CONSTITUCIONAL OU LEGAL DE RECEITAS- INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 835,'2013','3.5.2.2.0.00.00','TRANSFERÊNCIAS AO FUNDEB','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 836,'2013','3.5.2.2.4.00.00','TRANSFERÊNCIAS AO FUNDEB -  INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 837,'2013','3.5.2.3.0.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 838,'2013','3.5.2.3.1.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 839,'2013','3.5.2.3.3.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 840,'2013','3.5.2.3.4.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - INTER-OFSS - ESTADO ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 841,'2013','3.5.2.3.5.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - INTER-OFSS - MUNICÍPIO ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 842,'2013','3.5.2.4.0.00.00','OUTRAS TRANSFERÊNCIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 843,'2013','3.5.2.4.1.00.00','OUTRAS TRANSFERÊNCIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 844,'2013','3.5.2.4.3.00.00','OUTRAS TRANSFERÊNCIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 845,'2013','3.5.2.4.4.00.00','OUTRAS TRANSFERÊNCIAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 846,'2013','3.5.2.4.5.00.00','OUTRAS TRANSFERÊNCIAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 847,'2013','3.5.3.0.0.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES PRIVADAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 848,'2013','3.5.3.1.0.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES PRIVADAS SEM FINS LUCRATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 849,'2013','3.5.3.1.1.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES PRIVADAS SEM FINS LUCRATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 850,'2013','3.5.3.2.0.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES PRIVADAS COM FINS LUCRATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 851,'2013','3.5.3.2.1.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES PRIVADAS COM FINS LUCRATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 852,'2013','3.5.4.0.0.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES MULTIGOVERNAMENTAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 853,'2013','3.5.4.0.1.00.00','TRANSFERÊNCIAS A INSTITUIÇÕES MULTIGOVERNAMENTAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 854,'2013','3.5.5.0.0.00.00','TRANSFERÊNCIAS A CONSÓRCIOS PÚBLICOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 855,'2013','3.5.5.0.1.00.00','TRANSFERÊNCIAS A CONSÓRCIOS PÚBLICOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 856,'2013','3.5.6.0.0.00.00','TRANSFERÊNCIAS AO EXTERIOR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 857,'2013','3.5.6.0.1.00.00','TRANSFERÊNCIAS AO EXTERIOR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 858,'2013','3.5.7.0.0.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 859,'2013','3.5.7.1.0.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA A ENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 860,'2013','3.5.7.1.3.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA A ENTES - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 861,'2013','3.5.7.1.4.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA A ENTES - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 862,'2013','3.5.7.1.5.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA A ENTES - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 863,'2013','3.5.7.2.0.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA A CONSÓRCIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 864,'2013','3.5.7.2.1.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA A CONSÓRCIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 865,'2013','3.6.0.0.0.00.00','DESVALORIZAÇÃO E PERDA DE ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 866,'2013','3.6.1.0.0.00.00','REDUÇÃO A VALOR RECUPERÁVEL E AJUSTE PARA PERDAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 867,'2013','3.6.1.1.0.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 868,'2013','3.6.1.1.1.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INVESTIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 869,'2013','3.6.1.1.2.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INVESTIMENTOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 870,'2013','3.6.1.1.3.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INVESTIMENTOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 871,'2013','3.6.1.1.4.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INVESTIMENTOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 872,'2013','3.6.1.1.5.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INVESTIMENTOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 873,'2013','3.6.1.2.0.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 874,'2013','3.6.1.2.1.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE IMOBILIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 875,'2013','3.6.1.3.0.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INTANGÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 876,'2013','3.6.1.3.1.00.00','REDUÇÃO A VALOR RECUPERÁVEL DE INTANGÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 877,'2013','3.6.1.4.0.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE CRÉDITOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 878,'2013','3.6.1.4.1.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE CRÉDITOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 879,'2013','3.6.1.4.2.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE CRÉDITOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 880,'2013','3.6.1.4.3.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE CRÉDITOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 881,'2013','3.6.1.4.4.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE CRÉDITOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 882,'2013','3.6.1.4.5.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE CRÉDITOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 883,'2013','3.6.1.5.0.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE ESTOQUES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 884,'2013','3.6.1.5.1.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM AJUSTE DE PERDAS DE ESTOQUES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 885,'2013','3.6.2.0.0.00.00','PERDAS COM ALIENAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 886,'2013','3.6.2.1.0.00.00','PERDAS COM ALIENAÇÃO DE INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 887,'2013','3.6.2.1.1.00.00','PERDAS COM ALIENAÇÃO DE INVESTIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 888,'2013','3.6.2.2.0.00.00','PERDAS COM ALIENAÇÃO DE IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 889,'2013','3.6.2.2.1.00.00','PERDAS COM ALIENAÇÃO DE IMOBILIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 890,'2013','3.6.2.3.0.00.00','PERDAS COM ALIENAÇÃO DE INTANGÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 891,'2013','3.6.2.3.1.00.00','PERDAS COM ALIENAÇÃO DE INTANGÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 892,'2013','3.6.3.0.0.00.00','PERDAS INVOLUNTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 893,'2013','3.6.3.1.0.00.00','PERDAS INVOLUNTÁRIAS COM IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 894,'2013','3.6.3.1.1.00.00','PERDAS INVOLUNTÁRIAS COM IMOBILIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 895,'2013','3.6.3.2.0.00.00','PERDAS INVOLUNTÁRIAS COM INTANGÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 896,'2013','3.6.3.2.1.00.00','PERDAS INVOLUNTÁRIAS COM INTANGÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 897,'2013','3.6.3.3.0.00.00','PERDAS INVOLUNTÁRIAS COM ESTOQUES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 898,'2013','3.6.3.3.1.00.00','PERDAS INVOLUNTÁRIAS COM ESTOQUES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 899,'2013','3.6.3.9.0.00.00','OUTRAS PERDAS INVOLUNTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 900,'2013','3.6.3.9.1.00.00','OUTRAS PERDAS INVOLUNTÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 901,'2013','3.7.0.0.0.00.00','TRIBUTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 902,'2013','3.7.1.0.0.00.00','IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 903,'2013','3.7.1.1.0.00.00','IMPOSTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 904,'2013','3.7.1.1.1.00.00','IMPOSTOS- CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 905,'2013','3.7.1.2.0.00.00','TAXAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 906,'2013','3.7.1.2.1.00.00','TAXAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 907,'2013','3.7.1.3.0.00.00','CONTRIBUIÇÕES DE MELHORIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 908,'2013','3.7.1.3.1.00.00','CONTRIBUIÇÕES DE MELHORIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 909,'2013','3.7.2.0.0.00.00','CONTRIBUIÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 910,'2013','3.7.2.1.0.00.00','CONTRIBUIÇÕES SOCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 911,'2013','3.7.2.1.1.00.00','CONTRIBUIÇÕES SOCIAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 912,'2013','3.7.2.1.2.00.00','CONTRIBUIÇÕES SOCIAIS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 913,'2013','3.7.2.1.3.00.00','CONTRIBUIÇÕES SOCIAIS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 914,'2013','3.7.2.1.4.00.00','CONTRIBUIÇÕES SOCIAIS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 915,'2013','3.7.2.1.5.00.00','CONTRIBUIÇÕES SOCIAIS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 916,'2013','3.7.2.2.0.00.00','CONTRIBUIÇÕES DE INTERVENÇÃO NO DOMÍNIO ECONÔMICO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 917,'2013','3.7.2.2.1.00.00','CONTRIBUIÇÕES DE INTERVENÇÃO NO DOMÍNIO ECONÔMICO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 918,'2013','3.7.2.3.0.00.00','CONTRIBUIÇÃO PARA O CUSTEIO DO SERVIÇO DE ILUMINAÇÃO PÚBLICA - COSIP','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 919,'2013','3.7.2.3.1.00.00','CONTRIBUIÇÃO PARA O CUSTEIO DO SERVIÇO DE ILUMINAÇÃO PÚBLICA - COSIP - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 920,'2013','3.7.2.9.0.00.00','OUTRAS CONTRIBUIÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 921,'2013','3.7.2.9.1.00.00','OUTRAS CONTRIBUIÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 922,'2013','3.7.8.0.0.00.00','CUSTO COM TRIBUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 923,'2013','3.7.8.1.0.00.00','CUSTO DE MERCADORIAS VENDIDAS - TRIBUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 924,'2013','3.7.8.1.1.00.00','CUSTO DE MERCADORIAS VENDIDAS - TRIBUTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 925,'2013','3.7.8.1.2.00.00','CUSTO DE MERCADORIAS VENDIDAS - TRIBUTOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 926,'2013','3.7.8.1.3.00.00','CUSTO DE MERCADORIAS VENDIDAS - TRIBUTOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 927,'2013','3.7.8.1.4.00.00','CUSTO DE MERCADORIAS VENDIDAS - TRIBUTOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 928,'2013','3.7.8.1.5.00.00','CUSTO DE MERCADORIAS VENDIDAS - TRIBUTOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 929,'2013','3.7.8.2.0.00.00','CUSTO DE PRODUTOS VENDIDOS-TRIBUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 930,'2013','3.7.8.2.1.00.00','CUSTO DE PRODUTOS VENDIDOS-TRIBUTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 931,'2013','3.7.8.2.2.00.00','CUSTO DE PRODUTOS VENDIDOS-TRIBUTOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 932,'2013','3.7.8.2.3.00.00','CUSTO DE PRODUTOS VENDIDOS-TRIBUTOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 933,'2013','3.7.8.2.4.00.00','CUSTO DE PRODUTOS VENDIDOS-TRIBUTOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 934,'2013','3.7.8.2.5.00.00','CUSTO DE PRODUTOS VENDIDOS-TRIBUTOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 935,'2013','3.7.8.3.0.00.00','CUSTO DE SERVIÇOS PRESTADOS-TRIBUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 936,'2013','3.7.8.3.1.00.00','CUSTO DE SERVIÇOS PRESTADOS -TRIBUTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 937,'2013','3.7.8.3.2.00.00','CUSTO DE SERVIÇOS PRESTADOS -TRIBUTOS -INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 938,'2013','3.7.8.3.3.00.00','CUSTO DE SERVIÇOS PRESTADOS-TRIBUTOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 939,'2013','3.7.8.3.4.00.00','CUSTO DE SERVIÇOS PRESTADOS-TRIBUTOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 940,'2013','3.7.8.3.5.00.00','CUSTO DE SERVIÇOS PRESTADOS-TRIBUTOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 941,'2013','3.9.0.0.0.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 942,'2013','3.9.1.0.0.00.00','PREMIAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 943,'2013','3.9.1.1.0.00.00','PREMIAÇÕES CULTURAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 944,'2013','3.9.1.1.1.00.00','PREMIAÇÕES CULTURAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 945,'2013','3.9.1.2.0.00.00','PREMIAÇÕES ARTÍSTICAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 946,'2013','3.9.1.2.1.00.00','PREMIAÇÕES ARTÍSTICAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 947,'2013','3.9.1.3.0.00.00','PREMIAÇÕES CIENTIFICAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 948,'2013','3.9.1.3.1.00.00','PREMIAÇÕES CIENTIFICAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 949,'2013','3.9.1.4.0.00.00','PREMIAÇÕES DESPORTIVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 950,'2013','3.9.1.4.1.00.00','PREMIAÇÕES DESPORTIVAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 951,'2013','3.9.1.5.0.00.00','ORDENS HONORIFICAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 952,'2013','3.9.1.5.1.00.00','ORDENS HONORIFICAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 953,'2013','3.9.1.9.0.00.00','OUTRAS PREMIAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 954,'2013','3.9.1.9.1.00.00','OUTRAS PREMIAÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 955,'2013','3.9.2.0.0.00.00','RESULTADO NEGATIVO DE PARTICIPAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 956,'2013','3.9.2.1.0.00.00','RESULTADO NEGATIVO DE EQUIVALÊNCIA PATRIMONIAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 957,'2013','3.9.2.1.1.00.00','RESULTADO NEGATIVO DE EQUIVALÊNCIA PATRIMONIAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 958,'2013','3.9.2.1.2.00.00','RESULTADO NEGATIVO DE EQUIVALÊNCIA PATRIMONIAL - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 959,'2013','3.9.2.1.3.00.00','RESULTADO NEGATIVO DE EQUIVALÊNCIA PATRIMONIAL - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 960,'2013','3.9.2.1.4.00.00','RESULTADO NEGATIVO DE EQUIVALÊNCIA PATRIMONIAL - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 961,'2013','3.9.2.1.5.00.00','RESULTADO NEGATIVO DE EQUIVALÊNCIA PATRIMONIAL - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 962,'2013','3.9.4.0.0.00.00','INCENTIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 963,'2013','3.9.4.1.0.00.00','INCENTIVOS A EDUCAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 964,'2013','3.9.4.1.1.00.00','INCENTIVOS A EDUCAÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 965,'2013','3.9.4.2.0.00.00','INCENTIVOS A CIÊNCIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 966,'2013','3.9.4.2.1.00.00','INCENTIVOS A CIÊNCIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 967,'2013','3.9.4.3.0.00.00','INCENTIVOS A CULTURA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 968,'2013','3.9.4.3.1.00.00','INCENTIVOS A CULTURA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 969,'2013','3.9.4.4.0.00.00','INCENTIVOS AO ESPORTE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 970,'2013','3.9.4.4.1.00.00','INCENTIVOS AO ESPORTE - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 971,'2013','3.9.4.9.0.00.00','OUTROS INCENTIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 972,'2013','3.9.4.9.1.00.00','OUTROS INCENTIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 973,'2013','3.9.5.0.0.00.00','SUBVENÇÕES ECONÔMICAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 974,'2013','3.9.5.0.1.00.00','SUBVENÇÕES ECONÔMICAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 975,'2013','3.9.6.0.0.00.00','PARTICIPAÇÕES E CONTRIBUIÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 976,'2013','3.9.6.1.0.00.00','PARTICIPAÇÕES DE DEBÊNTURES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 977,'2013','3.9.6.1.1.00.00','PARTICIPAÇÕES DE DEBÊNTURES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 978,'2013','3.9.6.2.0.00.00','PARTICIPAÇÕES DE EMPREGADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 979,'2013','3.9.6.2.1.00.00','PARTICIPAÇÕES DE EMPREGADOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 980,'2013','3.9.6.3.0.00.00','PARTICIPAÇÕES DE ADMINISTRADORES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 981,'2013','3.9.6.3.1.00.00','PARTICIPAÇÕES DE ADMINISTRADORES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 982,'2013','3.9.6.4.0.00.00','PARTICIPAÇÕES DE PARTES BENEFICIARIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 983,'2013','3.9.6.4.1.00.00','PARTICIPAÇÕES DE PARTES BENEFICIARIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 984,'2013','3.9.6.5.0.00.00','PARTICIPAÇÕES DE INSTITUIÇÕES OU FUNDOS DE ASSISTÊNCIA OU PREVIDÊNCIA DE EMPREGADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 985,'2013','3.9.6.5.1.00.00','PARTICIPAÇÕES DE INSTITUIÇÕES OU FUNDOS DE ASSISTÊNCIA OU PREVIDÊNCIA DE EMPREGADOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 986,'2013','3.9.7.0.0.00.00','VPD DE CONSTITUIÇÃO DE PROVISÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 987,'2013','3.9.7.1.0.00.00','VPD DE PROVISÃO PARA RISCOS TRABALHISTAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 988,'2013','3.9.7.1.1.00.00','VPD DE PROVISÃO PARA RISCOS TRABALHISTAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 989,'2013','3.9.7.2.0.00.00',' VPD DE PROVISÕES MATEMÁTICAS PREVIDÊNCIÁRIAS A LONGO PRAZO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 990,'2013','3.9.7.2.1.00.00',' VPD DE PROVISÕES MATEMÁTICAS PREVIDÊNCIÁRIAS A LONGO PRAZO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 991,'2013','3.9.7.3.0.00.00','VPD DE PROVISÕES PARA RISCOS FISCAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 992,'2013','3.9.7.3.1.00.00','VPD DE PROVISÕES PARA RISCOS FISCAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 993,'2013','3.9.7.4.0.00.00','VPD DE PROVISÃO PARA RISCOS CÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 994,'2013','3.9.7.4.1.00.00','VPD DE PROVISÃO PARA RISCOS CÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 995,'2013','3.9.7.5.0.00.00','VPD DE PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 996,'2013','3.9.7.5.3.00.00','VPD DE PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 997,'2013','3.9.7.5.4.00.00','VPD DE PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 998,'2013','3.9.7.5.5.00.00','VPD DE PROVISÃO PARA REPARTIÇÃO DE CRÉDITOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES ( 999,'2013','3.9.7.6.0.00.00','VPD DE PROVISÃO PARA RISCOS DECORRENTES DE CONTRATOS DE PPP ','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1000,'2013','3.9.7.6.1.00.00','VPD DE PROVISÃO PARA RISCOS DECORRENTES DE CONTRATOS DE PPP - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1001,'2013','3.9.7.9.0.00.00','VPD DE OUTRAS PROVISÕES ','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1002,'2013','3.9.7.9.1.00.00','VPD DE OUTRAS PROVISÕES - CONSOLIDAÇÃO ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1003,'2013','3.9.8.0.0.00.00','CUSTO DE OUTRAS VPD','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1004,'2013','3.9.8.1.0.00.00','CUSTO DE MERCADORIAS VENDIDAS - OUTRAS VPD','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1005,'2013','3.9.8.1.1.00.00','CUSTO DE MERCADORIAS VENDIDAS - OUTRAS VPD - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1006,'2013','3.9.8.2.0.00.00','CUSTO DE PRODUTOS VENDIDOS - OUTRAS VPD','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1007,'2013','3.9.8.2.1.00.00','CUSTO DE PRODUTOS VENDIDOS - OUTRAS VPD - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1008,'2013','3.9.8.3.0.00.00','CUSTO DE SERVIÇOS PRESTADOS - OUTRAS VPD','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1009,'2013','3.9.8.3.1.00.00','CUSTO DE SERVIÇOS PRESTADOS - OUTRAS VPD - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1010,'2013','3.9.9.0.0.00.00','DIVERSAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1011,'2013','3.9.9.1.0.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1012,'2013','3.9.9.1.2.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1013,'2013','3.9.9.1.3.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1014,'2013','3.9.9.1.4.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1015,'2013','3.9.9.1.5.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1016,'2013','3.9.9.2.0.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1017,'2013','3.9.9.2.3.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1018,'2013','3.9.9.2.4.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1019,'2013','3.9.9.2.5.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1020,'2013','3.9.9.3.0.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM BONIFICAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1021,'2013','3.9.9.3.1.00.00','VARIAÇÃO PATRIMONIAL DIMINUTIVA COM BONIFICAÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1022,'2013','3.9.9.4.0.00.00','AMORTIZAÇÃO DE ÁGIO EM INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1023,'2013','3.9.9.4.1.00.00','AMORTIZAÇÃO DE ÁGIO EM INVESTIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1024,'2013','3.9.9.4.2.00.00','AMORTIZAÇÃO DE ÁGIO EM INVESTIMENTOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1025,'2013','3.9.9.4.3.00.00','AMORTIZAÇÃO DE ÁGIO EM INVESTIMENTOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1026,'2013','3.9.9.4.4.00.00','AMORTIZAÇÃO DE ÁGIO EM INVESTIMENTOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1027,'2013','3.9.9.4.5.00.00','AMORTIZAÇÃO DE ÁGIO EM INVESTIMENTOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1028,'2013','3.9.9.9.0.00.00','VARIAÇÕES PATRIMONIAIS DIMINUTIVAS DECORRENTES DE FATOS GERADORES DIVERSOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1029,'2013','3.9.9.9.1.00.00','VARIAÇÕES PATRIMONIAIS DIMINUTIVAS DECORRENTES DE FATOS GERADORES DIVERSOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1030,'2013','4.0.0.0.0.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1031,'2013','4.1.0.0.0.00.00','IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1032,'2013','4.1.1.0.0.00.00','IMPOSTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1033,'2013','4.1.1.1.0.00.00','IMPOSTOS SOBRE COMERCIO EXTERIOR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1034,'2013','4.1.1.1.1.00.00','IMPOSTOS SOBRE COMERCIO EXTERIOR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1035,'2013','4.1.1.2.0.00.00','IMPOSTOS SOBRE PATRIMÔNIO E A RENDA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1036,'2013','4.1.1.2.1.00.00','IMPOSTOS SOBRE PATRIMÔNIO E A RENDA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1037,'2013','4.1.1.3.0.00.00','IMPOSTOS SOBRE A PRODUÇÃO E A CIRCULAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1038,'2013','4.1.1.3.1.00.00','IMPOSTOS SOBRE A PRODUÇÃO E A CIRCULAÇÃO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1039,'2013','4.1.1.4.0.00.00','IMPOSTOS EXTRAORDINÁRIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1040,'2013','4.1.1.4.1.00.00','IMPOSTOS EXTRAORDINÁRIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1041,'2013','4.1.1.9.0.00.00','OUTROS IMPOSTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1042,'2013','4.1.1.9.1.00.00','OUTROS IMPOSTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1043,'2013','4.1.2.0.0.00.00','TAXAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1044,'2013','4.1.2.1.0.00.00','TAXAS PELO EXERCÍCIO DO PODER DE POLICIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1045,'2013','4.1.2.1.1.00.00','TAXAS PELO EXERCÍCIO DO PODER DE POLICIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1046,'2013','4.1.2.2.0.00.00','TAXAS PELA PRESTAÇÃO DE SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1047,'2013','4.1.2.2.1.00.00','TAXAS PELA PRESTAÇÃO DE SERVIÇOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1048,'2013','4.1.3.0.0.00.00','CONTRIBUIÇÕES DE MELHORIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1049,'2013','4.1.3.1.0.00.00','CONTRIBUIÇÃO DE MELHORIA PELA EXPANSÃO DA REDE DE ÁGUA POTÁVEL E ESGOTO SANITÁRIO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1050,'2013','4.1.3.1.1.00.00','CONTRIBUIÇÃO DE MELHORIA PELA EXPANSÃO DA REDE DE ÁGUA POTÁVEL E ESGOTO SANITÁRIO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1051,'2013','4.1.3.2.0.00.00','CONTRIBUIÇÃO DE MELHORIA PELA EXPANSÃO DA REDE DE ILUMINAÇÃO PÚBLICA NA CIDADE','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1052,'2013','4.1.3.2.1.00.00','CONTRIBUIÇÃO DE MELHORIA PELA EXPANSÃO DA REDE DE ILUMINAÇÃO PÚBLICA NA CIDADE - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1053,'2013','4.1.3.3.0.00.00','CONTRIBUIÇÃO DE MELHORIA PELA EXPANSÃO DE REDE DE ILUMINAÇÃO PÚBLICA RURAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1054,'2013','4.1.3.3.1.00.00','CONTRIBUIÇÃO DE MELHORIA PELA EXPANSÃO DE REDE DE ILUMINAÇÃO PÚBLICA RURAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1055,'2013','4.1.3.4.0.00.00','CONTRIBUIÇÃO DE MELHORIA PELA PAVIMENTAÇÃO E OBRAS COMPLEMENTARES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1056,'2013','4.1.3.4.1.00.00','CONTRIBUIÇÃO DE MELHORIA PELA PAVIMENTAÇÃO E OBRAS COMPLEMENTARES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1057,'2013','4.1.3.9.0.00.00','OUTRAS CONTRIBUIÇÕES DE MELHORIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1058,'2013','4.1.3.9.1.00.00','OUTRAS CONTRIBUIÇÕES DE MELHORIA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1059,'2013','4.2.0.0.0.00.00','CONTRIBUIÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1060,'2013','4.2.1.0.0.00.00','CONTRIBUIÇÕES SOCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1061,'2013','4.2.1.1.0.00.00','CONTRIBUIÇÕES SOCIAIS - RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1062,'2013','4.2.1.1.1.00.00','CONTRIBUIÇÕES SOCIAIS - RPPS - CONSOLIDAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1063,'2013','4.2.1.1.1.01.00','CONTRIBUIÇÕES PATRONAIS AO RPPS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1064,'2013','4.2.1.1.1.02.00','CONTRIBUIÇÃO DO SEGURADO AO RPPS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1065,'2013','4.2.1.1.1.03.00','CONTRIBUIÇÃO PREVIDENCIÁRIA PARA AMORTIZAÇÃO DO DÉFICIT ATUARIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1066,'2013','4.2.1.1.1.04.00','CONTRIBUIÇÕES PARA CUSTEIO DAS PENSÕES MILITARES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1067,'2013','4.2.1.1.1.97.00','(-) DEDUÇÕES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1068,'2013','4.2.1.1.1.99.00','OUTRAS CONTRIBUIÇÕES SOCIAIS - RPPS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1069,'2013','4.2.1.1.2.00.00','CONTRIBUIÇÕES SOCIAIS - RPPS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1070,'2013','4.2.1.1.3.00.00','CONTRIBUIÇÕES SOCIAIS - RPPS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1071,'2013','4.2.1.1.4.00.00','CONTRIBUIÇÕES SOCIAIS - RPPS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1072,'2013','4.2.1.1.5.00.00','CONTRIBUIÇÕES SOCIAIS - RPPS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1073,'2013','4.2.1.2.0.00.00','CONTRIBUIÇÕES SOCIAIS - RGPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1074,'2013','4.2.1.2.1.00.00','CONTRIBUIÇÕES SOCIAIS - RGPS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1075,'2013','4.2.1.2.2.00.00','CONTRIBUIÇÕES SOCIAIS - RGPS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1076,'2013','4.2.1.2.3.00.00','CONTRIBUIÇÕES SOCIAIS - RGPS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1077,'2013','4.2.1.2.4.00.00','CONTRIBUIÇÕES SOCIAIS - RGPS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1078,'2013','4.2.1.2.5.00.00','CONTRIBUIÇÕES SOCIAIS - RGPS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1079,'2013','4.2.1.3.0.00.00','CONTRIBUIÇÃO SOBRE A RECEITA OU O FATURAMENTO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1080,'2013','4.2.1.3.1.00.00','CONTRIBUIÇÃO SOBRE A RECEITA OU O FATURAMENTO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1081,'2013','4.2.1.4.0.00.00','CONTRIBUIÇÃO SOBRE O LUCRO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1082,'2013','4.2.1.4.1.00.00','CONTRIBUIÇÃO SOBRE O LUCRO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1083,'2013','4.2.1.5.0.00.00','CONTRIBUIÇÃO SOBRE RECEITA DE CONCURSO DE PROGNOSTICO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1084,'2013','4.2.1.5.1.00.00','CONTRIBUIÇÃO SOBRE RECEITA DE CONCURSO DE PROGNOSTICO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1085,'2013','4.2.1.6.0.00.00','CONTRIBUIÇÃO DO IMPORTADOR DE BENS OU SERVIÇOS DO EXTERIOR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1086,'2013','4.2.1.6.1.00.00','CONTRIBUIÇÃO DO IMPORTADOR DE BENS OU SERVIÇOS DO EXTERIOR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1087,'2013','4.2.1.9.0.00.00','OUTRAS CONTRIBUIÇÕES SOCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1088,'2013','4.2.1.9.1.00.00','OUTRAS CONTRIBUIÇÕES SOCIAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1089,'2013','4.2.2.0.0.00.00','CONTRIBUIÇÕES DE INTERVENÇÃO NO DOMÍNIO ECONÔMICO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1090,'2013','4.2.2.0.1.00.00','CONTRIBUIÇÕES DE INTERVENÇÃO NO DOMÍNIO ECONÔMICO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1091,'2013','4.2.3.0.0.00.00','CONTRIBUIÇÃO DE ILUMINAÇÃO PÚBLICA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1092,'2013','4.2.3.0.1.00.00','CONTRIBUIÇÃO DE ILUMINAÇÃO PÚBLICA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1093,'2013','4.2.4.0.0.00.00','CONTRIBUIÇÕES DE INTERESSE DAS CATÉGORIAS PROFISSIONAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1094,'2013','4.2.4.0.1.00.00','CONTRIBUIÇÕES DE INTERESSE DAS CATEGORIAS PROFISSIONAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1095,'2013','4.3.0.0.0.00.00','EXPLORAÇÃO E VENDA DE BENS, SERVIÇOS E DIREITOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1096,'2013','4.3.1.0.0.00.00','VENDA DE MERCADORIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1097,'2013','4.3.1.1.0.00.00','VENDA BRUTA DE MERCADORIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1098,'2013','4.3.1.1.1.00.00','VENDA BRUTA DE MERCADORIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1099,'2013','4.3.1.9.0.00.00','(-) DEDUÇÕES DA VENDA BRUTA DE MERCADORIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1100,'2013','4.3.1.9.1.00.00','(-) DEDUÇÕES DA VENDA BRUTA DE MERCADORIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1101,'2013','4.3.2.0.0.00.00','VENDA DE PRODUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1102,'2013','4.3.2.1.0.00.00','VENDA BRUTA DE PRODUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1103,'2013','4.3.2.1.1.00.00','VENDA BRUTA DE PRODUTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1104,'2013','4.3.2.9.0.00.00','(-) DEDUÇÕES DE VENDA BRUTA DE PRODUTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1105,'2013','4.3.2.9.1.00.00','(-) DEDUÇÕES DA VENDA BRUTA DE PRODUTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1106,'2013','4.3.3.0.0.00.00','EXPLORAÇÃO DE BENS E DIREITOS E PRESTAÇÃO DE SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1107,'2013','4.3.3.1.0.00.00','VALOR BRUTO DE EXPLORAÇÃO DE BENS E DIREITOS E PRESTAÇÃO DE SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1108,'2013','4.3.3.1.1.00.00','VALOR BRUTO DE EXPLORAÇÃO DE BENS, DIREITOS E PRESTAÇÃO DE SERVIÇOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1109,'2013','4.3.3.9.0.00.00','(-) DEDUÇÕES DO VALOR BRUTO DE EXPLORAÇÃO DE BENS, DIREITOS E PRESTAÇÃO DE SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1110,'2013','4.3.3.9.1.00.00','(-) DEDUÇÕES DO VALOR BRUTO DE EXPLORAÇÃO DE BENS, DIREITOS E PRESTAÇÃO DE SERVIÇOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1111,'2013','4.4.0.0.0.00.00','VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1112,'2013','4.4.1.0.0.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1113,'2013','4.4.1.1.0.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1114,'2013','4.4.1.1.1.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1115,'2013','4.4.1.1.3.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1116,'2013','4.4.1.1.4.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - INTER OFSS -ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1117,'2013','4.4.1.1.5.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1118,'2013','4.4.1.2.0.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS EXTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1119,'2013','4.4.1.2.1.00.00','JUROS E ENCARGOS DE EMPRÉSTIMOS EXTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1120,'2013','4.4.1.3.0.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS INTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1121,'2013','4.4.1.3.1.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1122,'2013','4.4.1.3.3.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1123,'2013','4.4.1.3.4.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1124,'2013','4.4.1.3.5.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1125,'2013','4.4.1.4.0.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS EXTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1126,'2013','4.4.1.4.1.00.00','JUROS E ENCARGOS DE FINANCIAMENTOS EXTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1127,'2013','4.4.2.0.0.00.00','JUROS E ENCARGOS DE MORA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1128,'2013','4.4.2.1.0.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1129,'2013','4.4.2.1.1.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1130,'2013','4.4.2.1.3.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1131,'2013','4.4.2.1.4.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1132,'2013','4.4.2.1.5.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1133,'2013','4.4.2.2.0.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS EXTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1134,'2013','4.4.2.2.1.00.00','JUROS E ENCARGOS DE MORA SOBRE EMPRÉSTIMOS E FINANCIAMENTOS EXTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1135,'2013','4.4.2.3.0.00.00','JUROS E ENCARGOS DE MORA SOBRE FORNECIMENTOS DE BENS E SERVIÇOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1136,'2013','4.4.2.3.1.00.00','JUROS E ENCARGOS DE MORA SOBRE FORNECIMENTOS DE BENS E SERVIÇOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1137,'2013','4.4.2.4.0.00.00','JUROS E ENCARGOS DE MORA SOBRE CRÉDITOS TRIBUTÁRIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1138,'2013','4.4.2.4.1.00.00','JUROS E ENCARGOS DE MORA SOBRE CRÉDITOS TRIBUTÁRIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1139,'2013','4.4.2.9.0.00.00','OUTROS JUROS E ENCARGOS DE MORA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1140,'2013','4.4.2.9.1.00.00','OUTROS JUROS E ENCARGOS DE MORA - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1141,'2013','4.4.3.0.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1142,'2013','4.4.3.1.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1143,'2013','4.4.3.1.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1144,'2013','4.4.3.1.3.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1145,'2013','4.4.3.1.4.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1146,'2013','4.4.3.1.5.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS INTERNOS CONCEDIDOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1147,'2013','4.4.3.2.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS EXTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1148,'2013','4.4.3.2.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE EMPRÉSTIMOS EXTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1149,'2013','4.4.3.3.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS INTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1150,'2013','4.4.3.3.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1151,'2013','4.4.3.3.3.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1152,'2013','4.4.3.3.4.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1153,'2013','4.4.3.3.5.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS INTERNOS CONCEDIDOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1154,'2013','4.4.3.4.0.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS EXTERNOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1155,'2013','4.4.3.4.1.00.00','VARIAÇÕES MONETÁRIAS E CAMBIAIS DE FINANCIAMENTOS EXTERNOS CONCEDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1156,'2013','4.4.3.9.0.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1157,'2013','4.4.3.9.1.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1158,'2013','4.4.3.9.3.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1159,'2013','4.4.3.9.4.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1160,'2013','4.4.3.9.5.00.00','OUTRAS VARIAÇÕES MONETÁRIAS E CAMBIAIS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1161,'2013','4.4.4.0.0.00.00','DESCONTOS FINANCEIROS OBTIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1162,'2013','4.4.4.0.1.00.00','DESCONTOS FINANCEIROS OBTIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1163,'2013','4.4.5.0.0.00.00','REMUNERAÇÃO DE DEPÓSITOS BANCÁRIOS E APLICAÇÕES FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1164,'2013','4.4.5.1.0.00.00','REMUNERAÇÃO DE DEPÓSITOS BANCÁRIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1165,'2013','4.4.5.1.1.00.00','REMUNERAÇÃO DE DEPÓSITOS BANCÁRIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1166,'2013','4.4.5.2.0.00.00','REMUNERAÇÃO DE APLICAÇÕES FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1167,'2013','4.4.5.2.1.00.00','REMUNERAÇÃO DE APLICAÇÕES FINANCEIRAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1168,'2013','4.4.9.0.0.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS - FINANCEIRAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1169,'2013','4.4.9.0.1.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS - FINANCEIRAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1170,'2013','4.5.0.0.0.00.00','TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1171,'2013','4.5.1.0.0.00.00','TRANSFERÊNCIAS INTRAGOVERNAMENTAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1172,'2013','4.5.1.1.0.00.00','TRANSFERÊNCIAS RECEBIDAS PARA A EXECUÇÃO ORÇAMENTÁRIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1173,'2013','4.5.1.1.2.00.00','TRANSFERÊNCIAS RECEBIDAS PARA A EXECUÇÃO ORÇAMENTÁRIA - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1174,'2013','4.5.1.2.0.00.00','TRANSFERÊNCIAS RECEBIDAS INDEPENDENTES DE EXECUÇÃO ORÇAMENTÁRIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1175,'2013','4.5.1.2.2.00.00','TRANSFERÊNCIAS RECEBIDAS INDEPENDENTES DE EXECUÇÃO ORÇAMENTÁRIA - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1176,'2013','4.5.1.3.0.00.00','TRANSFERENCIAS RECEBIDAS PARA COBERTURA DO DÉFICIT ATUARIAL DO RPPS POR APORTE PERIÓDICO ','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1177,'2013','4.5.1.3.2.00.00','TRANSFERENCIAS RECEBIDAS PARA COBERTURA DO DÉFICIT ATUARIAL DO RPPS POR APORTE PERIÓDICO - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1178,'2013','4.5.2.0.0.00.00','TRANSFERÊNCIAS INTER GOVERNAMENTAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1179,'2013','4.5.2.1.0.00.00','TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS DE RECEITAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1180,'2013','4.5.2.1.1.00.00','TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS DE RECEITAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1181,'2013','4.5.2.1.3.00.00','TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS DE RECEITAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1182,'2013','4.5.2.1.4.00.00','TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS DE RECEITAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1183,'2013','4.5.2.2.0.00.00','TRANSFERÊNCIAS DO FUNDEB','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1184,'2013','4.5.2.2.3.00.00','TRANSFERÊNCIAS DO FUNDEB - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1185,'2013','4.5.2.2.4.00.00','TRANSFERÊNCIAS DO FUNDEB - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1186,'2013','4.5.2.3.0.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1187,'2013','4.5.2.3.1.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1188,'2013','4.5.2.3.3.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1189,'2013','4.5.2.3.4.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1190,'2013','4.5.2.3.5.00.00','TRANSFERÊNCIAS VOLUNTÁRIAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1191,'2013','4.5.2.4.0.00.00','OUTRAS TRANSFERÊNCIAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1192,'2013','4.5.2.4.1.00.00','OUTRAS TRANSFERÊNCIAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1193,'2013','4.5.2.4.3.00.00','OUTRAS TRANSFERÊNCIAS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1194,'2013','4.5.2.4.4.00.00','OUTRAS TRANSFERÊNCIAS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1195,'2013','4.5.2.4.5.00.00','OUTRAS TRANSFERÊNCIAS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1196,'2013','4.5.3.0.0.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES PRIVADAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1197,'2013','4.5.3.1.0.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES PRIVADAS SEM FINS LUCRATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1198,'2013','4.5.3.1.1.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES PRIVADAS SEM FINS LUCRATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1199,'2013','4.5.3.2.0.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES PRIVADAS COM FINS LUCRATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1200,'2013','4.5.3.2.1.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES PRIVADAS COM FINS LUCRATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1201,'2013','4.5.4.0.0.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES MULTIGOVERNAMENTAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1202,'2013','4.5.4.0.1.00.00','TRANSFERÊNCIAS DAS INSTITUIÇÕES MULTIGOVERNAMENTAIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1203,'2013','4.5.5.0.0.00.00','TRANSFERÊNCIAS DE CONSÓRCIOS PÚBLICOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1204,'2013','4.5.5.0.1.00.00','TRANSFERÊNCIAS DE CONSÓRCIOS PÚBLICOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1205,'2013','4.5.6.0.0.00.00','TRANSFERÊNCIAS DO EXTERIOR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1206,'2013','4.5.6.0.1.00.00','TRANSFERÊNCIAS DO EXTERIOR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1207,'2013','4.5.7.0.0.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1208,'2013','4.5.7.1.0.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE ENTES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1209,'2013','4.5.7.1.3.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE ENTES - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1210,'2013','4.5.7.1.4.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE ENTES - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1211,'2013','4.5.7.1.5.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE ENTES - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1212,'2013','4.5.7.2.0.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE CONSÓRCIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1213,'2013','4.5.7.2.1.00.00','EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE CONSÓRCIOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1214,'2013','4.5.8.0.0.00.00','TRANSFERÊNCIAS DE PESSOAS FÍSICAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1215,'2013','4.5.8.0.1.00.00','TRANSFERÊNCIAS DE PESSOAS FÍSICAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1216,'2013','4.6.0.0.0.00.00','VALORIZAÇÃO E GANHOS COM ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1217,'2013','4.6.1.0.0.00.00','REAVALIAÇÃO DE ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1218,'2013','4.6.1.1.0.00.00','REAVALIAÇÃO DE IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1219,'2013','4.6.1.1.1.00.00','REAVALIAÇÃO DE IMOBILIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1220,'2013','4.6.1.2.0.00.00','REAVALIAÇÃO DE INTANGÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1221,'2013','4.6.1.2.1.00.00','REAVALIAÇÃO DE INTANGÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1222,'2013','4.6.1.9.0.00.00','REAVALIAÇÃO DE OUTROS ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1223,'2013','4.6.1.9.1.00.00','REAVALIAÇÃO DE OUTROS ATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1224,'2013','4.6.2.0.0.00.00','GANHOS COM ALIENAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1225,'2013','4.6.2.1.0.00.00','GANHOS COM ALIENAÇÃO DE INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1226,'2013','4.6.2.1.1.00.00','GANHOS COM ALIENAÇÃO DE INVESTIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1227,'2013','4.6.2.2.0.00.00','GANHOS COM ALIENAÇÃO DE IMOBILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1228,'2013','4.6.2.2.1.00.00','GANHOS COM ALIENAÇÃO DE IMOBILIZADO - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1229,'2013','4.6.2.3.0.00.00','GANHOS COM ALIENAÇÃO DE INTANGÍVEIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1230,'2013','4.6.2.3.1.00.00','GANHOS COM ALIENAÇÃO DE INTANGÍVEIS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1231,'2013','4.6.3.0.0.00.00','GANHOS COM INCORPORAÇÃO DE ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1232,'2013','4.6.3.1.0.00.00','GANHOS COM INCORPORAÇÃO DE ATIVOS POR DESCOBERTAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1233,'2013','4.6.3.1.1.00.00','GANHOS COM INCORPORAÇÃO DE ATIVOS POR DESCOBERTAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1234,'2013','4.6.3.2.0.00.00','GANHOS COM INCORPORAÇÃO DE ATIVOS POR NASCIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1235,'2013','4.6.3.2.1.00.00','GANHOS COM INCORPORAÇÃO DE ATIVOS POR NASCIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1236,'2013','4.6.3.3.0.00.00','GANHOS COM INCORPORAÇÃO DE VALORES APREENDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1237,'2013','4.6.3.3.1.00.00','GANHOS COM INCORPORAÇÃO DE ATIVOS APREENDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1238,'2013','4.6.3.9.0.00.00','OUTROS GANHOS COM INCORPORAÇÃO DE ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1239,'2013','4.6.3.9.1.00.00','OUTROS GANHOS COM INCORPORAÇÃO DE ATIVOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1240,'2013','4.9.0.0.0.00.00','OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1241,'2013','4.9.1.0.0.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA A CLASSIFICAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1242,'2013','4.9.1.0.1.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA A CLASSIFICAR - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1243,'2013','4.9.2.0.0.00.00','RESULTADO POSITIVO DE PARTICIPAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1244,'2013','4.9.2.1.0.00.00','RESULTADO POSITIVO DE EQUIVALÊNCIA PATRIMONIAL','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1245,'2013','4.9.2.1.1.00.00','RESULTADO POSITIVO DE EQUIVALÊNCIA PATRIMONIAL - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1246,'2013','4.9.2.1.2.00.00','RESULTADO POSITIVO DE EQUIVALÊNCIA PATRIMONIAL - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1247,'2013','4.9.2.1.3.00.00','RESULTADO POSITIVO DE EQUIVALÊNCIA PATRIMONIAL - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1248,'2013','4.9.2.1.4.00.00','RESULTADO POSITIVO DE EQUIVALÊNCIA PATRIMONIAL - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1249,'2013','4.9.2.1.5.00.00','RESULTADO POSITIVO DE EQUIVALÊNCIA PATRIMONIAL - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1250,'2013','4.9.2.2.0.00.00','DIVIDENDOS E RENDIMENTOS DE OUTROS INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1251,'2013','4.9.2.2.1.00.00','DIVIDENDOS E RENDIMENTOS DE OUTROS INVESTIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1252,'2013','4.9.7.0.0.00.00','REVERSÃO DE PROVISÕES E AJUSTES DE PERDAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1253,'2013','4.9.7.1.0.00.00','REVERSÃO DE PROVISÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1254,'2013','4.9.7.1.1.00.00','REVERSÃO DE PROVISÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1255,'2013','4.9.7.1.3.00.00','REVERSÃO DE PROVISÕES - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1256,'2013','4.9.7.1.4.00.00','REVERSÃO DE PROVISÕES - INTER OFSS - ESTADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1257,'2013','4.9.7.1.5.00.00','REVERSÃO DE PROVISÕES - INTER OFSS - MUNICÍPIOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1258,'2013','4.9.7.2.0.00.00','REVERSÃO DE AJUSTES DE PERDAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1259,'2013','4.9.7.2.1.00.00','REVERSÃO DE AJUSTES DE PERDAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1260,'2013','4.9.7.2.2.00.00','REVERSÃO DE AJUSTES DE PERDAS - INTRA OFSS ','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1261,'2013','4.9.7.2.3.00.00','REVERSÃO DE AJUSTES DE PERDAS -INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1262,'2013','4.9.7.2.4.00.00','REVERSÃO DE AJUSTES DE PERDAS -INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1263,'2013','4.9.7.2.5.00.00','REVERSÃO DE AJUSTES DE PERDAS -INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1264,'2013','4.9.9.0.0.00.00','DIVERSAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1265,'2013','4.9.9.1.0.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1266,'2013','4.9.9.1.2.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1267,'2013','4.9.9.1.3.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1268,'2013','4.9.9.1.4.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1269,'2013','4.9.9.1.5.00.00','COMPENSAÇÃO FINANCEIRA ENTRE RGPS/RPPS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1270,'2013','4.9.9.2.0.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1271,'2013','4.9.9.2.3.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1272,'2013','4.9.9.2.4.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1273,'2013','4.9.9.2.5.00.00','COMPENSAÇÃO FINANCEIRA ENTRE REGIMES PRÓPRIOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1274,'2013','4.9.9.3.0.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA COM BONIFICAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1275,'2013','4.9.9.3.1.00.00','VARIAÇÃO PATRIMONIAL AUMENTATIVA COM BONIFICAÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1276,'2013','4.9.9.4.0.00.00','AMORTIZAÇÃO DE DESÁGIO EM INVESTIMENTOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1277,'2013','4.9.9.4.1.00.00','AMORTIZAÇÃO DE DESÁGIO EM INVESTIMENTOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1278,'2013','4.9.9.4.2.00.00','AMORTIZAÇÃO DE DESÁGIO EM INVESTIMENTOS - INTRA OFSS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1279,'2013','4.9.9.4.3.00.00','AMORTIZAÇÃO DE DESÁGIO EM INVESTIMENTOS - INTER OFSS - UNIÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1280,'2013','4.9.9.4.4.00.00','AMORTIZAÇÃO DE DESÁGIO EM INVESTIMENTOS - INTER OFSS - ESTADO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1281,'2013','4.9.9.4.5.00.00','AMORTIZAÇÃO DE DESÁGIO EM INVESTIMENTOS - INTER OFSS - MUNICÍPIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1282,'2013','4.9.9.5.0.00.00','MULTAS ADMINISTRATIVAS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1283,'2013','4.9.9.5.1.00.00','MULTAS ADMINISTRATIVAS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1284,'2013','4.9.9.6.0.00.00','INDENIZAÇÕES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1285,'2013','4.9.9.6.1.00.00','INDENIZAÇÕES - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1286,'2013','4.9.9.7.0.00.00','VPA DECORRENTE ALIENAÇÃO BENS APREENDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1287,'2013','4.9.9.7.1.00.00','VPA DECORRENTE ALIENAÇÃO BENS APREENDIDOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1288,'2013','4.9.9.9.0.00.00','VARIAÇÕES PATRIMONIAIS AUMENTATIVAS DECORRENTES DE FATOS GERADORES DIVERSOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1289,'2013','4.9.9.9.1.00.00','VARIAÇÕES PATRIMONIAIS AUMENTATIVAS DECORRENTES DE FATOS GERADORES DIVERSOS - CONSOLIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1290,'2013','5.0.0.0.0.00.00','CONTROLES DA APROVAÇÃO DO PLANEJAMENTO E ORÇAMENTO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1291,'2013','5.1.0.0.0.00.00','PLANEJAMENTO APROVADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1292,'2013','5.1.1.0.0.00.00','PPA - APROVADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1293,'2013','5.1.2.0.0.00.00','PLOA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1294,'2013','5.2.0.0.0.00.00','ORÇAMENTO APROVADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1295,'2013','5.2.1.0.0.00.00','PREVISÃO DA RECEITA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1296,'2013','5.2.1.1.0.00.00','PREVISÃO INICIAL DA RECEITA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1297,'2013','5.2.1.2.0.00.00','ALTERAÇÃO DA PREVISÃO DA RECEITA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1298,'2013','5.2.1.2.1.00.00','PREVISÃO ADICIONAL DA RECEITA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1299,'2013','5.2.1.2.9.00.00','(-) ANULAÇÃO DA PREVISÃO DA RECEITA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1300,'2013','5.2.2.0.0.00.00','FIXAÇÃO DA DESPESA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1301,'2013','5.2.2.1.0.00.00','DOTAÇÃO ORÇAMENTÁRIA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1302,'2013','5.2.2.1.1.00.00','DOTAÇÃO INICIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1303,'2013','5.2.2.1.2.00.00','DOTAÇÃO ADICIONAL POR TIPO DE CREDITO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1304,'2013','5.2.2.1.2.01.00','CREDITO ADICIONAL - SUPLEMENTAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1305,'2013','5.2.2.1.2.02.00','CREDITO ADICIONAL - ESPECIAL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1306,'2013','5.2.2.1.2.02.01','CRÉDITOS ESPECIAIS ABERTOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1307,'2013','5.2.2.1.2.02.02','CRÉDITOS ESPECIAIS REABERTOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1308,'2013','5.2.2.1.2.02.03','CRÉDITOS ESPECIAIS REABERTOS - SUPLEMENTAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1309,'2013','5.2.2.1.2.03.00','CREDITO ADICIONAL - EXTRAORDINÁRIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1310,'2013','5.2.2.1.2.03.01','CRÉDITOS EXTRAORDINÁRIOS ABERTOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1311,'2013','5.2.2.1.2.03.02','CRÉDITOS EXTRAORDINÁRIOS REABERTOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1312,'2013','5.2.2.1.2.03.03','CRÉDITOS EXTRAORDINÁRIOS REABERTOS - SUPLEMENTAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1313,'2013','5.2.2.1.3.00.00','DOTAÇÃO ADICIONAL POR FONTE','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1314,'2013','5.2.2.1.9.00.00','CANCELAMENTO/REMANEJAMENTO DE DOTAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1315,'2013','5.2.2.2.0.00.00','MOVIMENTAÇÃO DE CRÉDITOS RECEBIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1316,'2013','5.2.2.2.1.00.00','DESCENTRALIZAÇÃO INTERNA DE CRÉDITOS - PROVISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1317,'2013','5.2.2.2.2.00.00','DESCENTRALIZAÇÃO EXTERNA DE CRÉDITOS - DESTAQUE','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1318,'2013','5.2.2.2.9.00.00','OUTRAS DESCENTRALIZAÇÕES DE CRÉDITOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1319,'2013','5.2.2.3.0.00.00','DETALHAMENTO DE CREDITO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1320,'2013','5.2.2.9.0.00.00','OUTROS CONTROLES DA DESPESA ORÇAMENTÁRIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1321,'2013','5.3.0.0.0.00.00','INSCRIÇÃO DE RESTOS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1322,'2013','5.3.1.0.0.00.00','INSCRIÇÃO DE RP NÃO PROCESSADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1323,'2013','5.3.1.1.0.00.00','RP NÃO PROCESSADOS INSCRITOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1324,'2013','5.3.1.2.0.00.00','RP NÃO PROCESSADOS - EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1325,'2013','5.3.1.3.0.00.00','RP NÃO PROCESSADOS RESTABELECIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1326,'2013','5.3.1.6.0.00.00','RP NÃO PROCESSADOS RECEBIDOS POR TRANSFERÊNCIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1327,'2013','5.3.1.7.0.00.00','RP NÃO PROCESSADOS - INSCRIÇÃO NO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1328,'2013','5.3.2.0.0.00.00','INSCRIÇÃO DE RP PROCESSADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1329,'2013','5.3.2.1.0.00.00','RP PROCESSADOS - INSCRITOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1330,'2013','5.3.2.2.0.00.00','RP PROCESSADOS - EXERCÍCIOS ANTERIORES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1331,'2013','5.3.2.6.0.00.00','RP PROCESSADOS RECEBIDOS POR TRANSFERÊNCIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1332,'2013','5.3.2.7.0.00.00','RP PROCESSADOS - INSCRIÇÃO NO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1333,'2013','6.0.0.0.0.00.00','CONTROLES DA EXECUÇÃO DO PLANEJAMENTO E ORÇAMENTO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1334,'2013','6.1.0.0.0.00.00','EXECUÇÃO DO PLANEJAMENTO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1335,'2013','6.1.1.0.0.00.00','EXECUÇÃO DO PPA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1336,'2013','6.1.2.0.0.00.00','EXECUÇÃO DO PLOA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1337,'2013','6.2.0.0.0.00.00','EXECUÇÃO DO ORÇAMENTO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1338,'2013','6.2.1.0.0.00.00','EXECUÇÃO DA RECEITA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1339,'2013','6.2.1.1.0.00.00','RECEITA A REALIZAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1340,'2013','6.2.1.2.0.00.00','RECEITA REALIZADA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1341,'2013','6.2.1.3.0.00.00','(-) DEDUÇÕES DA RECEITA ORÇAMENTÁRIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1342,'2013','6.2.2.0.0.00.00','EXECUÇÃO DA DESPESA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1343,'2013','6.2.2.1.0.00.00','DISPONIBILIDADES DE CREDITO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1344,'2013','6.2.2.1.1.00.00','CREDITO DISPONÍVEL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1345,'2013','6.2.2.1.2.00.00','CREDITO INDISPONÍVEL','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1346,'2013','6.2.2.1.3.00.00','CREDITO UTILIZADO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1347,'2013','6.2.2.1.3.01.00','CREDITO EMPENHADO A LIQUIDAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1348,'2013','6.2.2.1.3.02.00','CREDITO EMPENHADO EM LIQUIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1349,'2013','6.2.2.1.3.03.00','CREDITO EMPENHADO LIQUIDADO A PAGAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1350,'2013','6.2.2.1.3.04.00','CREDITO EMPENHADO LIQUIDADO PAGO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1351,'2013','6.2.2.1.3.05.00','EMPENHOS A LIQUIDAR INSCRITOS EM RESTOS A PAGAR NAO PROCESSADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1352,'2013','6.2.2.1.3.06.00','EMPENHOS EM LIQUIDACAO INSCRITOS EM RESTOS A PAGAR NAO PROCESSADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1353,'2013','6.2.2.1.3.07.00','EMPENHOS LIQUIDADOS INSCRITOS EM RESTOS A PAGAR PROCESSADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1354,'2013','6.2.2.1.3.99.00','(-) OUTROS CRÉDITOS UTILIZADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1355,'2013','6.2.2.2.0.00.00','MOVIMENTAÇÃO DE CRÉDITOS CONCEDIDOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1356,'2013','6.2.2.2.1.00.00','DESCENTRALIZAÇÃO INTERNA DE CRÉDITOS - PROVISÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1357,'2013','6.2.2.2.2.00.00','DESCENTRALIZAÇÃO EXTERNA DE CRÉDITOS - DESTAQUE','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1358,'2013','6.2.2.2.9.00.00','OUTRAS DESCENTRALIZAÇÕES DE CRÉDITOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1359,'2013','6.2.2.3.0.00.00','DETALHAMENTO DE CREDITO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1360,'2013','6.2.2.9.0.00.00','OUTROS CONTROLES DA DESPESA ORÇAMENTÁRIA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1361,'2013','6.3.0.0.0.00.00','EXECUÇÃO DE RESTOS A PAGAR','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1362,'2013','6.3.1.0.0.00.00','EXECUÇÃO DE RP NÃO PROCESSADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1363,'2013','6.3.1.1.0.00.00','RP NÃO PROCESSADOS A LIQUIDAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1364,'2013','6.3.1.2.0.00.00','RP NÃO PROCESSADOS EM LIQUIDAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1365,'2013','6.3.1.3.0.00.00','RP NÃO PROCESSADOS LIQUIDADOS A PAGAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1366,'2013','6.3.1.4.0.00.00','RP NÃO PROCESSADOS PAGOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1367,'2013','6.3.1.5.0.00.00','RP NÃO PROCESSADOS A LIQUIDAR BLOQUEADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1368,'2013','6.3.1.6.0.00.00','RP NÃO PROCESSADOS TRANSFERIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1369,'2013','6.3.1.7.0.00.00','RP NÃO PROCESSADOS - INSCRIÇÃO NO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1370,'2013','6.3.1.9.0.00.00','RP NÃO PROCESSADOS CANCELADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1371,'2013','6.3.2.0.0.00.00','EXECUÇÃO DE RP PROCESSADOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1372,'2013','6.3.2.1.0.00.00','RP PROCESSADOS A PAGAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1373,'2013','6.3.2.2.0.00.00','RP PROCESSADOS PAGOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1374,'2013','6.3.2.6.0.00.00','RP PROCESSADOS TRANSFERIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1375,'2013','6.3.2.7.0.00.00','RP PROCESSADOS - INSCRIÇÃO NO EXERCÍCIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1376,'2013','6.3.2.9.0.00.00','RP PROCESSADOS CANCELADOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1377,'2013','7.0.0.0.0.00.00','CONTROLES DEVEDORES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1378,'2013','7.1.0.0.0.00.00','ATOS POTENCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1379,'2013','7.1.1.0.0.00.00','ATOS POTENCIAIS ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1380,'2013','7.1.1.1.0.00.00','GARANTIAS E CONTRAGARANTIAS RECEBIDAS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1381,'2013','7.1.1.2.0.00.00','DIREITOS CONVENIADOS E OUTROS INSTRUMENTOS CONGÊNERES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1382,'2013','7.1.1.3.0.00.00','DIREITOS CONTRATUAIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1383,'2013','7.1.1.9.0.00.00','OUTROS ATOS POTENCIAIS ATIVOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1384,'2013','7.1.2.0.0.00.00','ATOS POTENCIAIS PASSIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1385,'2013','7.1.2.1.0.00.00','GARANTIAS E CONTRAGARANTIAS CONCEDIDAS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1386,'2013','7.1.2.2.0.00.00','OBRIGAÇÕES CONVENIADAS E OUTROS INSTRUMENTOS CONGÊNERES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1387,'2013','7.1.2.3.0.00.00','OBRIGAÇÕES CONTRATUAIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1388,'2013','7.1.2.9.0.00.00','OUTROS ATOS POTENCIAIS PASSIVOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1389,'2013','7.2.0.0.0.00.00','ADMINISTRAÇÃO FINANCEIRA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1390,'2013','7.2.1.0.0.00.00','DISPONIBILIDADES POR DESTINAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1391,'2013','7.2.1.1.0.00.00','CONTROLE DA DISPONIBILIDADE DE RECURSOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1392,'2013','7.2.1.2.0.00.00','LIMITE DE RESTOS A PAGAR POR DESTINAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1393,'2013','7.2.1.3.0.00.00','RECURSO DIFERIDO POR DESTINAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1394,'2013','7.2.2.0.0.00.00','PROGRAMAÇÃO FINANCEIRA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1395,'2013','7.2.3.0.0.00.00','INSCRIÇÃO DO LIMITE ORÇAMENTÁRIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1396,'2013','7.2.4.0.0.00.00','CONTROLES DA ARRECADAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1397,'2013','7.3.0.0.0.00.00','DIVIDA ATIVA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1398,'2013','7.3.1.0.0.00.00','CONTROLE DO ENCAMINHAMENTO DE CRÉDITOS PARA INSCRIÇÃO EM DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1399,'2013','7.3.2.0.0.00.00','CONTROLE DA INSCRIÇÃO DE CRÉDITOS EM DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1400,'2013','7.4.0.0.0.00.00','RISCOS FISCAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1401,'2013','7.4.1.0.0.00.00','CONTROLE DE PASSIVOS CONTINGENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1402,'2013','7.4.2.0.0.00.00','CONTROLE DOS DEMAIS RISCOS FISCAIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1403,'2013','7.8.0.0.0.00.00','CUSTOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1404,'2013','7.9.0.0.0.00.00','OUTROS CONTROLES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1405,'2013','8.0.0.0.0.00.00','CONTROLES CREDORES','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1406,'2013','8.1.0.0.0.00.00','EXECUÇÃO DOS ATOS POTENCIAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1407,'2013','8.1.1.0.0.00.00','EXECUÇÃO DOS ATOS POTENCIAIS ATIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1408,'2013','8.1.1.1.0.00.00','EXECUÇÃO DE GARANTIAS E CONTRAGARANTIAS RECEBIDAS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1409,'2013','8.1.1.2.0.00.00','EXECUÇÃO DE DIREITOS CONVENIADOS E OUTROS INSTRUMENTOS CONGÊNERES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1410,'2013','8.1.1.3.0.00.00','EXECUÇÃO DE DIREITOS CONTRATUAIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1411,'2013','8.1.1.9.0.00.00','EXECUÇÃO DE OUTROS ATOS POTENCIAIS ATIVOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1412,'2013','8.1.2.0.0.00.00','EXECUÇÃO DOS ATOS POTENCIAIS PASSIVOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1413,'2013','8.1.2.1.0.00.00','EXECUÇÃO DE GARANTIAS E CONTRAGARANTIAS CONCEDIDAS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1414,'2013','8.1.2.2.0.00.00','EXECUÇÃO DE OBRIGAÇÕES CONVENIADAS E OUTROS INSTRUMENTOS CONGÊNERES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1415,'2013','8.1.2.3.0.00.00','EXECUÇÃO DE OBRIGAÇÕES CONTRATUAIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1416,'2013','8.1.2.9.0.00.00','EXECUÇÃO DE OUTROS ATOS POTENCIAIS PASSIVOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1417,'2013','8.2.0.0.0.00.00','EXECUÇÃO DA ADMINISTRAÇÃO FINANCEIRA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1418,'2013','8.2.1.0.0.00.00','EXECUÇÃO DAS DISPONIBILIDADES POR DESTINAÇÃO','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1419,'2013','8.2.1.1.0.00.00','EXECUÇÃO DA DISPONIBILIDADE DE RECURSOS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1420,'2013','8.2.1.1.1.00.00','DISPONIBILIDADE POR DESTINAÇÃO DE RECURSOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1421,'2013','8.2.1.1.2.00.00','DISPONIBILIDADE POR DESTINAÇÃO DE RECURSOS COMPROMETIDA POR EMPENHO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1422,'2013','8.2.1.1.3.00.00','DISPONIBILIDADE POR DESTINAÇÃO DE RECURSOS COMPROMETIDA POR LIQUIDAÇÃO E ENTRADAS COMPENSATÓRIAS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1423,'2013','8.2.1.1.4.00.00','DISPONIBILIDADE POR DESTINAÇÃO DE RECURSOS UTILIZADA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1424,'2013','8.2.1.1.5.00.00','DISPONIBILIDADE POR DESTINAÇÃO DE RECURSOS COMPROMETIDA POR PROGRAMAÇÃO FINANCEIRA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1425,'2013','8.2.1.2.0.00.00','EXECUÇÃO FINANCEIRA DO LIMITE DE RESTOS A PAGAR','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1426,'2013','8.2.1.3.0.00.00','EXECUÇÃO DO RECURSO DIFERIDO POR DESTINAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1427,'2013','8.2.2.0.0.00.00','EXECUÇÃO DA PROGRAMAÇÃO FINANCEIRA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1428,'2013','8.2.3.0.0.00.00','EXECUÇÃO DO LIMITE ORÇAMENTÁRIO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1429,'2013','8.2.4.0.0.00.00','CONTROLES DA ARRECADAÇÃO','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1430,'2013','8.3.0.0.0.00.00','EXECUÇÃO DA DIVIDA ATIVA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1431,'2013','8.3.1.0.0.00.00','EXECUÇÃO DO ENCAMINHAMENTO DE CRÉDITOS PARA INSCRIÇÃO EM DIVIDA ATIVA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1432,'2013','8.3.1.1.0.00.00','CRÉDITOS A ENCAMINHAR PARA A DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1433,'2013','8.3.1.2.0.00.00','CRÉDITOS ENCAMINHADOS PARA A DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1434,'2013','8.3.1.3.0.00.00','CANCELAMENTO DE CRÉDITOS ENCAMINHADOS PARA A DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1435,'2013','8.3.2.0.0.00.00','EXECUÇÃO DA INSCRIÇÃO DE CRÉDITOS EM DIVIDA ATIVA','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1436,'2013','8.3.2.1.0.00.00','CRÉDITOS A INSCREVER EM DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1437,'2013','8.3.2.2.0.00.00','CRÉDITOS A INSCREVER EM DIVIDA ATIVA DEVOLVIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1438,'2013','8.3.2.3.0.00.00','CRÉDITOS INSCRITOS EM DIVIDA ATIVA A RECEBER','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1439,'2013','8.3.2.4.0.00.00','CRÉDITOS INSCRITOS EM DIVIDA ATIVA RECEBIDOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1440,'2013','8.3.2.5.0.00.00','BAIXA DE CRÉDITOS INSCRITOS EM DIVIDA ATIVA','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1441,'2013','8.4.0.0.0.00.00','EXECUÇÃO DOS RISCOS FISCAIS','S');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1442,'2013','8.4.1.0.0.00.00','EXECUÇÃO DE PASSIVOS CONTINGENTES','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1443,'2013','8.4.2.0.0.00.00','EXECUÇÃO DOS DEMAIS RISCOS FISCAIS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1444,'2013','8.8.0.0.0.00.00','APURAÇÃO DE CUSTOS','A');
INSERT INTO tcmgo.plano_contas_tcmgo (cod_plano, exercicio, estrutural, titulo, natureza) VALUES (1445,'2013','8.9.0.0.0.00.00','OUTROS CONTROLES','A');

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
  ( 2885
  , 364
  , 'FLVincularPlanoTCE.php'
  , 'incluir'
  , 32
  , ''
  , 'Vincular Plano TCM'
  , TRUE
  );


----------------
-- Ticket #20034
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
  ( 2886
  , 315
  , 'FLModelosRGF.php'
  , 'anexo6novo'
  , 56
  , 'Demonstrativo dos Restos a Pagar'
  , 'Anexo 6'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2886 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2259
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2259
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20322
----------------

CREATE TABLE stn.tributo_anexo_8(
    cod_tributo         INTEGER         NOT NULL,
    descricao           VARCHAR(50)     NOT NULL,
    CONSTRAINT pk_tributo_anexo_8       PRIMARY KEY (cod_tributo)
);
GRANT ALL ON stn.tributo_anexo_8 TO siamweb;

INSERT INTO stn.tributo_anexo_8 VALUES (1, 'Deduções da Receita IPTU');
INSERT INTO stn.tributo_anexo_8 VALUES (2, 'Deduções da Receita ITBI');
INSERT INTO stn.tributo_anexo_8 VALUES (3, 'Deduções da Receita ISS' );
INSERT INTO stn.tributo_anexo_8 VALUES (4, 'Deduções da Receita IRRF');
INSERT INTO stn.tributo_anexo_8 VALUES (5, 'Deduções da Receita ITR' );


CREATE TABLE stn.conta_dedutora_tributos(
    cod_tributo         INTEGER         NOT NULL,
    cod_receita         INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_conta_dedutora_tributos       PRIMARY KEY                     (cod_tributo, exercicio, cod_receita, timestamp),
    CONSTRAINT fk_conta_dedutora_tributos_1     FOREIGN KEY                     (cod_tributo)
                                                REFERENCES stn.tributo_anexo_8  (cod_tributo),
    CONSTRAINT fk_conta_dedutora_tributos_2     FOREIGN KEY                     (cod_receita, exercicio)
                                                REFERENCES orcamento.receita    (cod_receita, exercicio)
);
GRANT ALL ON stn.conta_dedutora_tributos TO siamweb;

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
  ( 2887
  , 406
  , 'FMVincularContasDeducoesReceitaImpostos.php'
  , 'vincular'
  , 22
  , ''
  , 'Vincular Contas Dedutoras da Receita de Impostos'
  , TRUE
  );

