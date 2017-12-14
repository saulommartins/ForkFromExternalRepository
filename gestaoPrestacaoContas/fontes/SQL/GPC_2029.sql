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
* Versao 2.02.9
*
* Fabio Bertoldi - 20140723
*
*/

-----------------------
-- Ticket #21839 #21814
-----------------------

CREATE TABLE tcepb.uniorcam (
    exercicio           CHAR(4)     NOT NULL,
    num_unidade         INTEGER     NOT NULL,
    num_orgao           INTEGER     NOT NULL,
    cgm_ordenador       INTEGER             ,
    natureza_juridica   INTEGER             ,
    CONSTRAINT pk_uniorcam          PRIMARY KEY       (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_uniorcam_1        FOREIGN KEY       (cgm_ordenador)
                                    REFERENCES sw_cgm (numcgm)
);
GRANT ALL ON TABLE tcepb.uniorcam TO urbem;

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
     ( 2973
     , 365
     , 'FMManterConfiguracaoUnidadeOrcamentaria.php'
     , 'manter'
     , 94
     , ''
     , 'Configurar Unidade Orçamentária'
     , TRUE
     );


----------------
-- Ticket #21760
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
SELECT 6    AS cod_gestao
     , 55   AS cod_modulo
     , 9    AS cod_relatorio
     , 'Anexo III A'
     , 'LHTCEMGRelatorioAnexoIIIA.php'
 WHERE 0 = (
                    SELECT COUNT(1)
                      FROM administracao.relatorio
                     WHERE cod_gestao    = 6
                       AND cod_modulo    = 55
                       AND cod_relatorio = 9
                  )
     ;

----------------
-- Ticket #21762
----------------

UPDATE administracao.relatorio
   SET arquivo = 'LHTCEMGRelatorioAnexo4.php'
 WHERE cod_relatorio = 5
   AND cod_modulo    = 55
   AND cod_gestao    = 6
     ;


----------------
-- Ticket #21757
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_type
      WHERE typname = 'tp_anexo_valor_conta'
          ;

    IF NOT FOUND THEN
        CREATE TYPE tcemg.tp_anexo_valor_conta AS (
                                                    grupo           INTEGER
                                                  , subgrupo        INTEGER
                                                  , item            INTEGER
                                                  , exercicio       CHAR(4)
                                                  , cod_conta       INTEGER
                                                  , nivel           INTEGER
                                                  , cod_estrutural  VARCHAR
                                                  , masc_red        VARCHAR
                                                  , descricao       VARCHAR
                                                  , tipo            CHARACTER
                                                  , ini             NUMERIC
                                                  , cred_adi        NUMERIC
                                                  , atu             NUMERIC
                                                  , no_bi           NUMERIC
                                                  , ate_bi          NUMERIC
                                                  , pct             NUMERIC
                                                  );
    ELSE
        DROP type tcemg.tp_anexo_valor_conta CASCADE;
        CREATE TYPE tcemg.tp_anexo_valor_conta AS (
                                                    grupo           INTEGER
                                                  , subgrupo        INTEGER
                                                  , item            INTEGER
                                                  , exercicio       CHAR(4)
                                                  , cod_conta       INTEGER
                                                  , nivel           INTEGER
                                                  , cod_estrutural  VARCHAR
                                                  , masc_red        VARCHAR
                                                  , descricao       VARCHAR
                                                  , tipo            CHARACTER
                                                  , ini             NUMERIC
                                                  , cred_adi        NUMERIC
                                                  , atu             NUMERIC
                                                  , no_bi           NUMERIC
                                                  , ate_bi          NUMERIC
                                                  , pct             NUMERIC
                                                  );
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #21747
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
     ( 2979
     , 484
     , 'FLRelatorioRazaoDespesa.php'
     , 'consultar'
     , 13
     , ''
     , 'Razão da Despesa'
     , TRUE
     );


INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 10
     , 'Razão da Despesa'
     , 'LHTCEMGRelatorioRazaoDespesa.php'
     );
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 11
     , 'Razão da Despesa - Restos a Pagar'
     , 'LHTCEMGRelatorioRazaoDespesaRestosPagar.php'
     );
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 12
     , 'Razão da Despesa - Despesa Extra Orçamentaria'
     , 'LHTCEMGRelatorioRazaoDespesaDespesaExtraOrc.php'
     );
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 13
     , 'Razão da Despesa - Receita Extra Orçamentaria'
     , 'LHTCEMGRelatorioRazaoDespesaReceitaExtraOrc.php'
     );


----------------
-- Ticket #21940
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
     ( 485
     , 62
     , 'Transparência'
     , 'instancias/transparencia/'
     , 3
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
     ( 2980
     , 485
     , 'FLExportacaoDespesa.php'
     , 'consultar'
     , 1
     , ''
     , 'Despesa'
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
     ( 2981
     , 485
     , 'FLExportacaoReceita.php'
     , 'consultar'
     , 2
     , ''
     , 'Receita'
     , TRUE
     );

