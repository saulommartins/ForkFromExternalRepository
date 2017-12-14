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
* Versao 2.03.0
*
* Fabio Bertoldi - 20140911
*
*/

----------------
-- Ticket #20152
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM orcamento.conta_receita
      WHERE exercicio = '2015'
          ;
    IF NOT FOUND THEN
        DELETE FROM administracao.configuracao WHERE cod_modulo IN (8,9,10) AND exercicio::integer > 2014;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #20152
----------------

INSERT
  INTO orcamento.funcao
     ( exercicio
     , cod_funcao
     , descricao
     )
SELECT '2014' AS exercicio
     , cod_funcao
     , descricao
  FROM orcamento.funcao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.funcao
              WHERE exercicio = '2014'
           )
     ;

INSERT
  INTO orcamento.funcao
     ( exercicio
     , cod_funcao
     , descricao
     )
SELECT '2015' AS exercicio
     , cod_funcao
     , descricao
  FROM orcamento.funcao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.funcao
              WHERE exercicio = '2015'
           )
     ;

INSERT
  INTO orcamento.funcao
     ( exercicio
     , cod_funcao
     , descricao
     )
SELECT '2016' AS exercicio
     , cod_funcao
     , descricao
  FROM orcamento.funcao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.funcao
              WHERE exercicio = '2016'
           )
     ;

INSERT
  INTO orcamento.funcao
     ( exercicio
     , cod_funcao
     , descricao
     )
SELECT '2017' AS exercicio
     , cod_funcao
     , descricao
  FROM orcamento.funcao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.funcao
              WHERE exercicio = '2017'
           )
     ;


INSERT
  INTO orcamento.subfuncao
     ( exercicio
     , cod_subfuncao
     , descricao
     )
SELECT '2014' AS exercicio
     , cod_subfuncao
     , descricao
  FROM orcamento.subfuncao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.subfuncao
              WHERE exercicio = '2014'
           )
     ;

INSERT
  INTO orcamento.subfuncao
     ( exercicio
     , cod_subfuncao
     , descricao
     )
SELECT '2015' AS exercicio
     , cod_subfuncao
     , descricao
  FROM orcamento.subfuncao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.subfuncao
              WHERE exercicio = '2015'
           )
     ;

INSERT
  INTO orcamento.subfuncao
     ( exercicio
     , cod_subfuncao
     , descricao
     )
SELECT '2016' AS exercicio
     , cod_subfuncao
     , descricao
  FROM orcamento.subfuncao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.subfuncao
              WHERE exercicio = '2016'
           )
     ;

INSERT
  INTO orcamento.subfuncao
     ( exercicio
     , cod_subfuncao
     , descricao
     )
SELECT '2017' AS exercicio
     , cod_subfuncao
     , descricao
  FROM orcamento.subfuncao
 WHERE exercicio = '2013'
   AND 0 = (
             SELECT COUNT(1)
               FROM orcamento.subfuncao
              WHERE exercicio = '2017'
           )
     ;

