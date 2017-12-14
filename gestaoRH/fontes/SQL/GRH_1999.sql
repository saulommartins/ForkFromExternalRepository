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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 1.99.9
*/

----------------
-- Ticket #16831
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2011'
        AND parametro  = 'cnpj'
        AND valor      = '15424948000141'
          ;
    IF FOUND THEN

        INSERT
          INTO pessoal.cargo_padrao 
             ( SELECT cod_cargo
                    , 0
                    , '1900-01-01'
                 FROM pessoal.cargo
                WHERE cod_cargo NOT IN ( SELECT cod_cargo
                                           FROM pessoal.cargo_padrao
                                       )
             );

        INSERT
          INTO pessoal.cbo_cargo
             ( SELECT 1371          AS cod_cbo
                    , pessoal.cargo.cod_cargo
                    , '1900-01-01'  AS timestamp
                 FROM pessoal.cargo
                WHERE cargo.cod_cargo NOT IN ( SELECT cod_cargo
                                                 FROM pessoal.cbo_cargo
                                             )
             );

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #17279
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       from pg_type
      where typname = lower('colunasDirfPlanoSaude')
          ;
    IF FOUND THEN
        DROP TYPE colunasDirfPlanoSaude CASCADE;
    END IF;

    CREATE TYPE colunasDirfPlanoSaude AS (
        registro        INTEGER,
        cod_contrato    INTEGER,
        nom_cgm         VARCHAR,
        numcgm          INTEGER,
        cpf             VARCHAR,
        valor           NUMERIC
    );

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

