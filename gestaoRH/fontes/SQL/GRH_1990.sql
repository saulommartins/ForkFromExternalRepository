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
* $Id:$
*
* Versão 1.99.0
*/

----------------
-- Ticket #16606
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSql VARCHAR;
BEGIN

    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'evento'
        AND pg_attribute.attname  = 'apresentar_contracheque'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
          ;

    IF NOT FOUND THEN

        stSql := 'SELECT atualizarbanco(\'ALTER TABLE folhapagamento.evento ADD COLUMN apresentar_contracheque BOOLEAN NOT NULL DEFAULT FALSE;\');';
        EXECUTE stSql;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #16636
----------------

SELECT atualizarbanco('ALTER TABLE pessoal.cargo ALTER COLUMN descricao TYPE VARCHAR(100);');


----------------
-- Ticket #16630
----------------

UPDATE administracao.acao  SET nom_acao = 'Crédito Pré-Aprovado' WHERE cod_acao = 2229;


----------------
-- Ticket #16582
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSql VARCHAR;
BEGIN

    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'dependente'
        AND pg_attribute.attname  = 'dependente_prev'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
          ;

    IF NOT FOUND THEN

        stSql := 'SELECT atualizarbanco(\'ALTER TABLE pessoal.dependente ADD COLUMN dependente_prev BOOLEAN NOT NULL DEFAULT FALSE\');';
        EXECUTE stSql;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


------------------------------------------------------------------------
-- INSERINDO FUNÇÃO pega0camposalariofuncao COMO FUNÇÃO INTERNA (r44753)
------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.funcao
      WHERE cod_modulo     = 27
        AND cod_biblioteca = 1
        AND nom_funcao     = 'pega0camposalariofuncao'
          ;

    IF NOT FOUND THEN

        INSERT
          INTO administracao.funcao
        VALUES (
               27
             , 1
             , (  SELECT MAX (cod_funcao) + 1
                    FROM administracao.funcao
                   WHERE cod_modulo = 27
                     AND cod_biblioteca = 1
               )
             , 4
             , 'pega0camposalariofuncao'
             );

    END IF;


    PERFORM 1
       FROM administracao.funcao
      WHERE cod_modulo     = 27
        AND cod_biblioteca = 1
        AND nom_funcao     = 'pega1PercentualAposentadoria'
          ;

    IF NOT FOUND THEN

        INSERT
          INTO administracao.funcao
        VALUES (
               27
             , 1
             , (  SELECT MAX (cod_funcao) + 1
                    FROM administracao.funcao
                   WHERE cod_modulo = 27
                     AND cod_biblioteca = 1
               )
             , 4
             , 'pega1PercentualAposentadoria'
             );

    END IF;


    PERFORM 1
       FROM administracao.funcao
      WHERE cod_modulo     = 27
        AND cod_biblioteca = 1
        AND nom_funcao     = 'pega1ServidorCid'
          ;

    IF NOT FOUND THEN

        INSERT
          INTO administracao.funcao
        VALUES (
               27
             , 1
             , (  SELECT MAX (cod_funcao) + 1
                    FROM administracao.funcao
                   WHERE cod_modulo = 27
                     AND cod_biblioteca = 1
               )
             , 4
             , 'pega1ServidorCid'
             );

    END IF;


    PERFORM 1
       FROM administracao.funcao
      WHERE cod_modulo     = 27
        AND cod_biblioteca = 1
        AND nom_funcao     = 'pega1ServidorCid'
          ;

    IF NOT FOUND THEN

        INSERT
          INTO administracao.funcao
        VALUES (
               27
             , 1
             , (  SELECT MAX (cod_funcao) + 1
                    FROM administracao.funcao
                   WHERE cod_modulo = 27
                     AND cod_biblioteca = 1
               )
             , 4
             , 'pega1PercentualPensionista'
             );

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

