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
-- Ticket #21889
----------------

UPDATE administracao.tabelas_rh SET sequencia = 4 WHERE schema_cod = 1 AND nome_tabela = 'caso_causa'    ;
UPDATE administracao.tabelas_rh SET sequencia = 2 WHERE schema_cod = 1 AND nome_tabela = 'causa_rescisao';
UPDATE administracao.tabelas_rh SET sequencia = 3 WHERE schema_cod = 1 AND nome_tabela = 'periodo_caso'  ;


-----------------------------------------
-- REORDENANDO MENU GPC - Silvia 20140925
-----------------------------------------

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
     ( 63
     , 0
     , 'TCE - PE'
     , 'TCEPE/'
     , 85
     , 6
     , TRUE
     );


UPDATE administracao.modulo SET ordem = 32 WHERE cod_modulo = 60;
UPDATE administracao.modulo SET ordem = 34 WHERE cod_modulo = 59;
UPDATE administracao.modulo SET ordem = 36 WHERE cod_modulo = 53;
UPDATE administracao.modulo SET ordem = 37 WHERE cod_modulo = 54;
UPDATE administracao.modulo SET ordem = 41 WHERE cod_modulo = 61;
UPDATE administracao.modulo SET ordem = 43 WHERE cod_modulo = 36;
UPDATE administracao.modulo SET ordem = 45 WHERE cod_modulo = 62;
UPDATE administracao.modulo SET ordem = 47 WHERE cod_modulo = 56;
UPDATE administracao.modulo SET ordem = 49 WHERE cod_modulo = 55;
UPDATE administracao.modulo SET ordem = 70 WHERE cod_modulo = 57;
UPDATE administracao.modulo SET ordem = 80 WHERE cod_modulo = 41;
UPDATE administracao.modulo SET ordem = 90 WHERE cod_modulo = 32;
UPDATE administracao.modulo SET ordem = 91 WHERE cod_modulo = 49;
UPDATE administracao.modulo SET ordem = 92 WHERE cod_modulo = 46;
UPDATE administracao.modulo SET ordem = 93 WHERE cod_modulo = 52;
UPDATE administracao.modulo SET ordem = 94 WHERE cod_modulo = 47;
UPDATE administracao.modulo SET ordem = 95 WHERE cod_modulo = 45;
UPDATE administracao.modulo SET ordem = 96 WHERE cod_modulo = 42;
UPDATE administracao.modulo SET ordem = 97 WHERE cod_modulo = 48;
UPDATE administracao.modulo SET ordem = 98 WHERE cod_modulo = 58;


-----------------------------------------------------------------
-- COPIA DE CONFIGURACOES DO MODULO 2 PARA 2015 - Silvia 20140926
-----------------------------------------------------------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2015' AS exercicio
     , cod_modulo
     , parametro
     , valor
  FROM administracao.configuracao AS proximo
 WHERE proximo.cod_modulo = 2
   AND proximo.exercicio  = '2014'
   AND NOT EXISTS (
                    SELECT 1
                      FROM administracao.configuracao
                     WHERE exercicio  = '2015'
                       AND cod_modulo = proximo.cod_modulo
                       AND parametro  = proximo.parametro
                  )
     ;

