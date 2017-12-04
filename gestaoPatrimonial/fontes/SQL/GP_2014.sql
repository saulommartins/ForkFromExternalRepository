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
* Fabio Bertoldi - 20130108
*
*/

----------------
-- Ticket #19926
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_proc
      WHERE proname ilike '%fn_depreciacao_acumula%'
          ;
    IF FOUND THEN
        DROP FUNCTION patrimonio.fn_depreciacao_acumulada(INTEGER);
    END IF;

    PERFORM 1
       FROM pg_type
      WHERE typname ilike '%colunasDepreciacaoAcumulada%'
          ;
    IF FOUND THEN
        DROP TYPE colunasDepreciacaoAcumulada;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

CREATE TYPE colunasDepreciacaoAcumulada AS ( cod_bem            INTEGER
                                           , vl_acumulado       NUMERIC(14,2)
                                           , vl_atualizado      NUMERIC(14,2)
                                           , vl_bem             NUMERIC(14,2)
                                           , min_competencia    VARCHAR
                                           , max_competencia    VARCHAR
                                           );

