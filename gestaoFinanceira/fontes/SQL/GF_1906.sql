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
* $Id: GF_1906.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.90.6.
*/

 INSERT INTO administracao.acao
              (cod_acao
            , cod_funcionalidade
            , nom_arquivo
            , parametro
            , ordem
            , complemento_acao
            , nom_acao)
       SELECT 2237
            , 168
            ,'FLDespesasSIOPE.php'
            , 'imprimir'
            , 42
            , ''
            , 'Relatório Despesas SIOPE'
        WHERE 0 = (
                    SELECT COUNT(1)
                      FROM administracao.acao
                     WHERE cod_acao = 2237
                  );

----------------
-- Ticket #12568
----------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     SELECT 2247
          , 168
          , 'FLEvolucaoDespesa.php'
          , 'imprimir'
          , 43
          , ''
          , 'Demonstrativo da Evolução da Despesa'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 2247
                );

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     SELECT 2
          , 8
          , 2
          , 'Demonstrativo da Evolução da Despesa'
          , 'relatorioEvolucaoDespesa.rptdesign'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.relatorio
                   WHERE cod_gestao    = 2
                     AND cod_modulo    = 8
                     AND cod_relatorio = 2
                );


DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2050;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2050;

DELETE
  FROM administracao.acao 
 WHERE cod_acao = 2050;

DELETE 
  FROM administracao.auditoria 
 WHERE cod_acao = 2175;

DELETE 
  FROM administracao.permissao
 WHERE cod_acao = 2175;

DELETE 
  FROM administracao.acao 
 WHERE cod_acao = 2175;

----------------
-- Ticket #12603
----------------

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 413
          , 30
          , 'Saldos'
          , '/instancias/saldos/'
          , 11
         WHERE 0 = (SELECT COUNT(1)
                           FROM administracao.funcionalidade
                          WHERE cod_funcionalidade = 413);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     SELECT 2253
          , 413
          , 'FLConsultarSaldos.php'
          , 'consultar'
          , 1
          , ''
          , 'Consultar Saldo'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 2253
                );


-----------------
-- Ticket #12659
-----------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     SELECT 2254
          , 168
          , 'FLEvolucaoReceita.php'
          , 'imprimir'
          , 44
          , ''
          , 'Demonstrativo da Evolução da Receita'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 2254
                );


INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     SELECT 2
          , 8
          , 3
          , 'Demonstrativo da Evolução da Receita'
          , 'relatorioEvolucaoReceita.rptdesign'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.relatorio
                   WHERE cod_gestao    = 2
                     AND cod_modulo    = 8
                     AND cod_relatorio = 3
                );

----- Tira espaços do campo

UPDATE orcamento.conta_despesa
   SET descricao= btrim(descricao);


-----------------
-- Ticket #12847
-----------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , ordem
          , nom_acao)
     SELECT 2263
          , 209
          , 'FLRelacaoEmpenho.php'
          , 13
          , 'Relação de Empenhos'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 2263
                );


INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     SELECT 2
          , 10
          , 1
          , 'Relação de Empenhos'
          , 'relacaoDeEmpenho.rptdesign'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.relatorio
                   WHERE cod_gestao    = 2
                     AND cod_modulo    = 10
                     AND cod_relatorio = 1
                );

