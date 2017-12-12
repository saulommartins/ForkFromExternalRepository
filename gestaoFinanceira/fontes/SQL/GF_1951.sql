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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GF_1951.sql 64421 2016-02-19 12:14:17Z fabio $
*
* Versão 1.95.1
*/

----------------
-- Ticket #15311
----------------

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2722;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2722;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2722
   AND nom_acao = 'Estimativa da Receita';

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade = 438
   AND nom_funcionalidade = 'Relatórios';

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 438
         , 43
         , 'Relatórios'
         , 'instancias/relatorios/'
         , 21
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2722
          , 438
          , 'FLEstimativaReceitaPPA.php'
          , 'emitir'
          , 1
          , ''
          , 'Estimativa da Receita'
          );

DELETE
  FROM administracao.relatorio
 WHERE cod_modulo = 43;

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 43
          , 1
          , 'Estimativa de Receita'
          , 'estimativaReceitaPPA.rptdesign'
          );


--------------------------------------------------------------
-- ALTERACOES NOS RELACIONAMENTOS DO PPA COM orcamento.unidade
--------------------------------------------------------------

DROP TABLE ppa.programa_orgao_responsavel;

ALTER TABLE ppa.programa_dados ADD COLUMN exercicio_unidade CHAR(4) NOT NULL;
ALTER TABLE ppa.programa_dados ADD COLUMN num_unidade       INTEGER NOT NULL;
ALTER TABLE ppa.programa_dados ADD COLUMN num_orgao         INTEGER NOT NULL;
ALTER TABLE ppa.programa_dados ADD CONSTRAINT fk_programa_dados_3 FOREIGN KEY                  (exercicio_unidade, num_unidade, num_orgao)
                                                                  REFERENCES orcamento.unidade (exercicio, num_unidade, num_orgao);

ALTER TABLE ppa.acao_dados DROP CONSTRAINT fk_acao_dados_9;
ALTER TABLE ppa.acao_dados DROP COLUMN exercicio_unidade;
ALTER TABLE ppa.acao_dados DROP COLUMN num_unidade;
ALTER TABLE ppa.acao_dados DROP COLUMN num_orgao;

CREATE TABLE ppa.acao_unidade_executora (
    cod_acao                INTEGER         NOT NULL,
    timestamp_acao_dados    TIMESTAMP       NOT NULL,
    exercicio_unidade       CHAR(4)         NOT NULL,
    num_unidade             INTEGER         NOT NULL,
    num_orgao               INTEGER         NOT NULL,
    CONSTRAINT pk_acao_unidade_executora    PRIMARY KEY                  (cod_acao, timestamp_acao_dados, exercicio_unidade, num_unidade, num_orgao),
    CONSTRAINT fk_acao_unidade_executora_1  FOREIGN KEY                  (cod_acao, timestamp_acao_dados)
                                            REFERENCES ppa.acao_dados    (cod_acao, timestamp_acao_dados),
    CONSTRAINT fk_acao_unidade_executora_2  FOREIGN KEY                  (exercicio_unidade, num_unidade, num_orgao)
                                            REFERENCES orcamento.unidade (exercicio, num_unidade, num_orgao)
 );

GRANT ALL ON ppa.acao_unidade_executora TO GROUP urbem;


---------------------------------------------------------------------------
-- COLUNA tipo_percentual_informadm EM ppa.ppa_estimativa_orcamentaria_base
---------------------------------------------------------------------------

ALTER TABLE ppa.ppa_estimativa_orcamentaria_base ADD COLUMN tipo_percentual_informado CHAR(1) NOT NULL;
ALTER TABLE ppa.ppa_estimativa_orcamentaria_base ADD CONSTRAINT ck_ppa_estimativa_orcamentaria_base_3 CHECK (tipo_percentual_informado IN ('A','S'));


-----------------------------------------
-- ALTERANDO COLUNAS DE PERCENTUAL P/ 7,2
-----------------------------------------

ALTER TABLE ppa.ppa_estimativa_orcamentaria_base ALTER COLUMN percentual_ano_1 TYPE NUMERIC(7,2);
ALTER TABLE ppa.ppa_estimativa_orcamentaria_base ALTER COLUMN percentual_ano_2 TYPE NUMERIC(7,2);
ALTER TABLE ppa.ppa_estimativa_orcamentaria_base ALTER COLUMN percentual_ano_3 TYPE NUMERIC(7,2);
ALTER TABLE ppa.ppa_estimativa_orcamentaria_base ALTER COLUMN percentual_ano_4 TYPE NUMERIC(7,2);


----------------------------------------
-- RETIRANDO COLUNA num_acao DE ppa.acao
----------------------------------------

ALTER TABLE ppa.acao DROP COLUMN num_acao;


--------------------------------------------------------------------------------
-- ALTERANDO DOLUNA formula_calculo P/ forma_calculo EM ppa.programa_indicadores
--------------------------------------------------------------------------------

ALTER TABLE ppa.programa_indicadores ADD   COLUMN forma_calculo VARCHAR(100);
UPDATE      ppa.programa_indicadores SET          forma_calculo = formula_calculo;
ALTER TABLE ppa.programa_indicadores ALTER COLUMN forma_calculo SET NOT NULL;
ALTER TABLE ppa.programa_indicadores DROP  COLUMN formula_calculo;
ALTER TABLE ppa.programa_indicadores DROP  COLUMN unidade_medida;


---------------------------------------------------------------------------------------------
-- REMOVENDO COLUNA descricao DE ppa.acao, ADICIONANDO COLUNAS P/ descricao EM ppa.acao_dados
---------------------------------------------------------------------------------------------

ALTER TABLE ppa.acao DROP COLUMN descricao;

ALTER TABLE ppa.acao_dados ADD COLUMN titulo     VARCHAR(480) NOT NULL;
ALTER TABLE ppa.acao_dados ADD COLUMN descricao  VARCHAR(480) NOT NULL;
ALTER TABLE ppa.acao_dados ADD COLUMN finalidade VARCHAR(480) NOT NULL;


-------------------------------------------------------
-- ADICIONANDO COLUNA quantidade EM ppa.acao_quantidade
-------------------------------------------------------

ALTER TABLE ppa.acao_quantidade ADD COLUMN quantidade NUMERIC(5,2) NOT NULL;


-------------------------------------------------------------------------------
-- ALTERANDO QUANTIDADE DE CARACTERES DA COLUNA descricao EM orcamento.programa
-------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_views
      WHERE viewname = 'vw_rl_relacao_despesa';

    IF FOUND THEN

        DROP VIEW orcamento.vw_rl_relacao_despesa;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

ALTER TABLE orcamento.programa ALTER COLUMN descricao TYPE VARCHAR(480);


------------------------------------------------
-- REMOVENDO MODULO ldo CADASTRADO INDEVIDAMENTE
------------------------------------------------

DROP SCHEMA ldo CASCADE;

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN (  SELECT cod_acao
                        FROM administracao.acao
                       WHERE cod_funcionalidade IN (  SELECT cod_funcionalidade
                                                        FROM administracao.funcionalidade
                                                       WHERE cod_modulo = 44
                                                   )
                   );

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN (  SELECT cod_acao
                        FROM administracao.acao
                        WHERE cod_funcionalidade IN (  SELECT cod_funcionalidade
                                                         FROM administracao.funcionalidade
                                                        WHERE cod_modulo = 44
                                                    )
                   );

DELETE
  FROM administracao.acao
 WHERE cod_funcionalidade IN (  SELECT cod_funcionalidade
                                  FROM administracao.funcionalidade
                                 WHERE cod_modulo = 44
                             );

DELETE
  FROM administracao.funcionalidade
 WHERE cod_modulo = 44;
 
-- PROGRAMA

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2721;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2721;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2721
   AND nom_acao = 'Elaborar Estimativa da Receita';

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2351;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2351;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2351
   AND nom_acao = 'Incluir Programa';

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2352;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2352;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2352
   AND nom_acao = 'Alterar Programa';

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2353;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2353;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2353
   AND nom_acao = 'Excluir Programa';

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade = 433
   AND nom_funcionalidade = 'Programa';

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 433
         , 43
         , 'Programa'
         , 'instancias/programas/'
         , 10
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2351
          , 433
          , 'FMManterPrograma.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Programa'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2352
          , 433
          , 'FLManterPrograma.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Programa'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2353
          , 433
          , 'FLManterPrograma.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Programa'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2721
          , 432
          , 'FMElaborarEstimativaReceita.php'
          , 'elaborar'
          , 5
          , ''
          , 'Elaborar Estimativa da Receita'
          );

