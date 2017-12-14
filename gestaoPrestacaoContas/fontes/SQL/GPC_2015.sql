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
* Versao 2.01.5
*
* Fabio Bertoldi - 20120713
*
*/

--------------------
-- TCERN.NOTA_FISCAL
--------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stTeste     VARCHAR;
BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'nota_fiscal'
        AND schemaname = 'tcern'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tcern.nota_fiscal (
            cod_nota_liquidacao     INTEGER     NOT NULL,
            cod_entidade            INTEGER     NOT NULL,
            exercicio               CHAR(4)     NOT NULL,
            nro_nota                VARCHAR(12)    NOT NULL,
            nro_serie               VARCHAR(12)    NOT NULL,
            data_emissao            DATE        NOT NULL,
            cod_validacao           VARCHAR(50) NOT NULL,
            modelo                  VARCHAR(3)  NOT NULL,
            CONSTRAINT pk_nota_fiscal                   PRIMARY KEY                         (cod_nota_liquidacao,cod_entidade,exercicio),
            CONSTRAINT fk_nota_fiscal_nota_liquidacao_2 FOREIGN KEY                         (cod_nota_liquidacao,cod_entidade,exercicio)
                                                        REFERENCES empenho.nota_liquidacao  (cod_nota,cod_entidade,exercicio)
        );

        GRANT ALL ON tcern.nota_fiscal                      TO GROUP siamweb;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20010
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
  ( 2860
  , 314
  , 'FLModelosRREO.php'
  , 'anexo2novo'
  , 52
  , 'Demonstrativo da Execução das Despesas por Função/Subfunção'
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
     , 2860 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 1502
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1502
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20015
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
  ( 2862
  , 314
  , 'FLModelosRREO.php'
  , 'anexo7novo'
  , 57
  , 'Demonstrativo dos Restos a Pagar por Poder e Órgão'
  , 'Anexo 7'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2862 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2225
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2225
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20018
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
     ( 2863
     , 314
     , 'FLModelosRREO.php'
     , 'anexo9novo'
     , 59
     , 'Demonstrativo das Receitas de Operações de Crédito e Despesas de Capital'
     , 'Anexo 9'
     , TRUE
     );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2863 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2230
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2230
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
     , 43
     , 'RREO - Anexo 9 - Demonstrativo das Receitas de Operações de Crédito e Despesas de Capital'
     , 'RREOAnexo9_paisagem.rptdesign'
     );


----------------
-- Ticket #20019
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
  ( 2864
  , 314
  , 'OCGeraRREOAnexo13.php'
  , 'anexo10novo'
  , 60
  , 'Demonstrativo da Projeção Atuarial do RPPS'
  , 'Anexo 10'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2864 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2422
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2422
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20036
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
  ( 2865
  , 315
  , 'FLModelosRGF.php'
  , 'anexo7'
  , 57
  , 'Demonstrativo Simplificado do Relatório de Gestão Fiscal'
  , 'Anexo 7'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2865 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2265
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2265
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20020
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
  ( 2866
  , 314
  , 'FLModelosRREO.php'
  , 'anexo11novo'
  , 61
  , 'Demonstrativo da Receita de Alienação de Ativos e Aplicação dos Recursos'
  , 'Anexo 11'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2866 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2189
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2189
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
     , 44
     , 'RREO - Anexo 11 - Demonstrativo da Receita de Alienação de Ativos e Aplicação dos Recursos'
     , 'RREOAnexo14_paisagem.rptdesign'
     );


----------------
-- Ticket #20049
----------------

INSERT
  INTO tcmgo.contrato_modalidade_licitacao 
  ( cod_modalidade
  , descricao
  )
  VALUES
  ( 0
  , 'Não se aplica (Ex: Despesas com Pessoal)'
  );


----------------
-- Ticket #20011
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
  ( 2869
  , 314
  , 'FLModelosRREO.php'
  , 'anexo3novo'
  , 53
  , ''
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
     , 2869 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 1503
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1503
   AND ano_exercicio = '2013'
     ;


----------------
-- Ticket #20013
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
     ( 2870
     , 314
     , 'FLModelosRREO.php'
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
     , 2870 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2190
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2190
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
     , 42
     , 'Anexo 6'
     , 'RREOAnexo6_paisagem.rptdesign'
     );


----------------
-- Ticket #20126
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
  ( 2872
  , 406
  , 'FMConfigurarAnexo4.php'
  , 'configurar'
  , 21
  , ''
  , 'Configurar RREO Anexo 4'
  , TRUE
  );


CREATE TABLE stn.aporte_recurso_rpps_grupo (
    cod_grupo       INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    descricao       VARCHAR     NOT NULL,
    CONSTRAINT pk_aporte_recurso_rpps_grupo         PRIMARY KEY (cod_grupo, exercicio)
);
GRANT ALL ON stn.aporte_recurso_rpps_grupo TO siamweb;

CREATE TABLE stn.aporte_recurso_rpps (
    cod_aporte      INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_grupo       INTEGER     NOT NULL,
    descricao       VARCHAR     NOT NULL,
    CONSTRAINT pk_aporte_recurso_rpps               PRIMARY KEY                                 (cod_aporte, exercicio),
    CONSTRAINT fk_aporte_recurso_grupo_1            FOREIGN KEY                                 (cod_grupo, exercicio)
                                                    REFERENCES stn.aporte_recurso_rpps_grupo    (cod_grupo, exercicio)
);
GRANT ALL ON stn.aporte_recurso_rpps TO siamweb;

CREATE TABLE stn.aporte_recurso_rpps_receita (
    cod_aporte      INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_receita     INTEGER     NOT NULL,
    timestamp       TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_aporte_recurso_rpps_receita       PRIMARY KEY                         (cod_aporte, exercicio, cod_receita, timestamp),
    CONSTRAINT fk_aporte_recurso_grupo_recurso_1    FOREIGN KEY                         (cod_aporte, exercicio)
                                                    REFERENCES stn.aporte_recurso_rpps  (cod_aporte, exercicio),
    CONSTRAINT fk_aporte_recurso_grupo_recurso_2    FOREIGN KEY                         (cod_receita, exercicio)
                                                    REFERENCES orcamento.receita        (cod_receita, exercicio)
);
GRANT ALL ON stn.aporte_recurso_rpps_receita TO siamweb;


INSERT INTO stn.aporte_recurso_rpps_grupo (cod_grupo, exercicio, descricao) VALUES (1, '2013', 'Plano Financeiro'    );
INSERT INTO stn.aporte_recurso_rpps_grupo (cod_grupo, exercicio, descricao) VALUES (2, '2013', 'Plano Previdenciário');

INSERT INTO stn.aporte_recurso_rpps (cod_aporte, exercicio, descricao, cod_grupo) VALUES (1, '2013', 'Recursos para Cobertura de Insuficiências Financeiras', 1);
INSERT INTO stn.aporte_recurso_rpps (cod_aporte, exercicio, descricao, cod_grupo) VALUES (2, '2013', 'Recursos para Formação de Reserva'                    , 1);
INSERT INTO stn.aporte_recurso_rpps (cod_aporte, exercicio, descricao, cod_grupo) VALUES (3, '2013', 'Outros Aportes para o RPPS'                           , 1);

INSERT INTO stn.aporte_recurso_rpps (cod_aporte, exercicio, descricao, cod_grupo) VALUES (4, '2013', 'Recursos para Cobertura de Déficit Financeiro', 2);
INSERT INTO stn.aporte_recurso_rpps (cod_aporte, exercicio, descricao, cod_grupo) VALUES (5, '2013', 'Recursos para Cobertura de Déficit Atuarial'  , 2);
INSERT INTO stn.aporte_recurso_rpps (cod_aporte, exercicio, descricao, cod_grupo) VALUES (6, '2013', 'Outros Aportes para o RPPS'                   , 2);


----------------
-- Ticket #19755
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
  ( 59
  , 0
  , 'MANAD'
  , 'manad/'
  , 94
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
  ( 475
  , 59
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
  ( 2851
  , 475
  , 'FMConfiguracaoMANAD.php'
  , 'alterar'
  , 1
  , ''
  , 'Alterar Configuração'
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
  ( 2868
  , 475
  , 'FMConfiguracaoUnidadeOrcamentariaMANAD.php'
  , 'alterar'
  , 2
  , ''
  , 'Alterar Tipo Unidade Orçamentária '
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
  ( 476
  , 59
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
  ( 2852
  , 476
  , 'FMExportarMANAD.php'
  , 'exportar'
  , 1
  , ''
  , 'Exportar Arquivos '
  , TRUE
  );

GRANT ALL ON SCHEMA manad TO GROUP siamweb;


----------------
-- Ticket #20017
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
     , 49
     , 'RREO - Anexo 8 - Demonstrativo das Receitas e Despesas com Manutenção e Desenvolvimento do Ensino - MDE'
     , 'RREOAnexo8NovoConsorcio.rptdesign'
     );


----------------
-- Ticket #20045
----------------

CREATE TABLE tcmgo.arquivo_ext(
    cod_plano       INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    mes             INTEGER     NOT NULL,
    sequencial      INTEGER     NOT NULL,
    CONSTRAINT pk_arquivo_ext   PRIMARY KEY                                 (cod_plano, exercicio, mes, sequencial),
    CONSTRAINT fk_arquivo_ext_1 FOREIGN KEY                                 (cod_plano, exercicio)
                                REFERENCES contabilidade.plano_analitica    (cod_plano, exercicio)
);
GRANT ALL ON tcmgo.arquivo_ext TO siamweb;


----------------
-- Ticket #20023
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
  ( 2867
  , 314
  , 'FLModelosRREO.php'
  , 'anexo14novo'
  , 64
  , 'Demonstrativo Simplificado do Relatório Resumido da Execução Orçamentária'
  , 'Anexo 14'
  , TRUE
  );
INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT numcgm
     , 2867 AS cod_acao
     , '2013' AS ano_exercicio
  FROM administracao.permissao
 WHERE cod_acao = 2264
GROUP BY numcgm
     ;
DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2264
   AND ano_exercicio = '2013'
     ;

