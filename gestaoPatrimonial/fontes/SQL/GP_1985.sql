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
* Versão 1.98.5
*/

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
VALUES
     ( 6
     , '2009'
     , 'valor_minimo_depreciacao'
     , ''
     );
INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
VALUES
     ( 6
     , '2009'
     , 'competencia_depreciacao'
     , ''
     );


----------------
-- Ticket #16268
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
SELECT 2776
     , 28
     , 'FLRelatorioDepreciacoes.php'
     , 'imprimir'
     , 90
     , ''
     , 'Relatório de Depreciações'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 2776
           );

UPDATE administracao.acao
   SET nom_arquivo = 'FLRelatorioDepreciacoes.php'
     , nom_acao    = 'Relatório de Depreciações'
 WHERE cod_acao    = 2776
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 3
     , 6
     , 16
     , 'Relatório de Depreciações'
     , 'depreciacao.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 3
                AND cod_modulo    = 6
                AND cod_relatorio = 16
           );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES
     ( 2780
     , 28
     , 'FLRelatorioDepreciacoesAcumuladas.php'
     , ''
     , 100
     , ''
     , 'Relatório de Depreciações Acumuladas'
     );
INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 3
     , 6
     , 17
     , 'Relatório de Depreciações Acumuladas'
     , 'depreciacaoAcumulada.rptdesign'
     );


-------------------------------------------
-- RECRIANCO ACOES P/ ROTINAS DE INVENTARIO
-------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2403
          , 447
          , 'FMManterInventario.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2404
          , 447
          , 'FLManterInventario.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2405
          , 447
          , 'FLManterInventario.php'
          , 'anular'
          , 3
          , ''
          , 'Anular Inventário'
          );

INSERT INTO administracao.acao     ----------------
              ( cod_acao           -- Ticket #12654
              , cod_funcionalidade ----------------
              , nom_arquivo
              , parametro
              , ordem
              , complemento_acao
              , nom_acao )
         VALUES ( 2713
              , 447
              , 'FLManterInventario.php'
              , 'processar'
              , 4
              , ''
              , 'Processar Inventário'
              );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2406
          , 447
          , 'FLManterInventario.php'
          , 'abertura'
          , 5
          , ''
          , 'Termo de Abertura'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2407
          , 447
          , 'FLManterInventario.php'
          , 'inventario'
          , 6
          , ''
          , 'Relatório do Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2408
          , 447
          , 'FLManterInventario.php'
          , 'historico'
          , 7
          , ''
          , 'Histórico do Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2409
          , 447
          , 'FLManterInventario.php'
          , 'encerramento'
          , 8
          , ''
          , 'Termo de Encerramento'
          );

