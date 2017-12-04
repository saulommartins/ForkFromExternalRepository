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
* Versao 2.03.4
*
* Fabio Bertoldi - 20141208
*
*/

----------------
-- Ticket #22456
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
SELECT 6
     , 36
     , 60
     , 'Dem. Dívida Consolidada Líquida'
     , 'RGFAnexo2Mensal.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 60
           )
     ;


----------------
-- Ticket #22457
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
SELECT 6
     , 36
     , 12
     , 'Relatório RGF Anexo 3 Mensal'
     , 'RGFAnexo3Mensal.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 12
           )
     ;


----------------
-- Ticket #22458
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 59
     , 'RGF - Anexo 4 - Demonstrativo das Operações de Crédito'
     , 'RGFAnexo4NovoMensal.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 59
           )
     ;


----------------
-- Ticket #22332
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 61
     , 'Dem. Receitas e Despesas Previdenciárias do RPPS'
     , 'RREOAnexo4_2015.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 61
           )
     ;


----------------
-- Ticket #22469
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 62
     , 'RREO - Anexo 9 - Demonstrativo das Receitas de Operações de Crédito e Despesas de Capital'
     , 'RREOAnexo9_mensal.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 62
           )
     ;


----------------
-- Ticket #22471
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_type
      WHERE typname = 'tp_rreo_valor_conta'
          ;
    IF NOT FOUND THEN
        CREATE TYPE stn.tp_rreo_valor_conta AS ( grupo          INTEGER
                                               , subgrupo       INTEGER
                                               , item           INTEGER
                                               , exercicio      CHARACTER
                                               , cod_conta      INTEGER
                                               , nivel          INTEGER
                                               , cod_estrutural VARCHAR
                                               , masc_red       VARCHAR
                                               , descricao      VARCHAR
                                               , tipo           CHARACTER
                                               , ini            NUMERIC
                                               , cred_adi       NUMERIC
                                               , atu            NUMERIC
                                               , no_bi          NUMERIC
                                               , ate_bi         NUMERIC
                                               , pct            NUMERIC
                                               );

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #22335
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 63
     , 'RREO - Anexo 8 - Demonstrativo das Receitas e Despesas com Manutenção e Desenvolvimentolvimento do Ensino-MDE'
     , 'RREOAnexo8_2015.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 63
           )
     ;


----------------
-- Ticket #22333
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 64
     , 'RREO - Anexo 6 - Demonstrativo do Resultado Primário'
     , 'RREOAnexo6_2015.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 64
           )
     ;


----------------
-- Ticket #22337
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 65
     , 'RREO - Anexo 11 - Demonstrativo da Receita de Alienação de Ativos e Aplicação dos Recursos'
     , 'RREOAnexo11_2015.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 65
           )
     ;


----------------
-- Ticket #22542
----------------

UPDATE administracao.acao SET complemento_acao='Demonstrativo da Receita Corrente Líquida' WHERE cod_acao=2869;


----------------
-- Ticket #22331
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 66
     , 'Relatório RREO Anexo 2'
     , 'RREOAnexo2_2015.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 66
           )
     ;

