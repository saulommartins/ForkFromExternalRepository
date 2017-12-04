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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Id: GPC_012.sql 59612 2014-09-02 12:00:51Z gelson $
*
* VersÃ£o 012.
*/


----------------
-- Ticket #12842
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao
          )
     VALUES ( 2260
          , 364
          , 'FMManterNotasFiscais.php'
          , 'incluir'
          , 21
          ,'Incluir Notas Fiscais'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao
          )
     VALUES ( 2261
          , 364
          , 'FLManterNotasFiscais.php'
          , 'alterar'
          , 22
          ,'Alterar Notas Fiscais'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao
          )
     VALUES ( 2262
          , 364
          , 'FLManterNotasFiscais.php'
          , 'excluir'
          , 23
          ,'Excluir Notas Fiscais'
          );

----------------
-- Ticket #12904
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao)
     VALUES ( 2266
          , 402
          , 'FLObrasServicos.php'
          , 'imprimir'
          , 2
          , 'Obras e Serviços'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES ( 6
          , 42
          , 1
          , 'Obras e Serviços'
          , 'obrasServicosTCMGO.rptdesign'
          );

----------------
-- Ticket #12916
----------------

DELETE FROM administracao.permissao
 WHERE cod_acao = 2231;

DELETE FROM administracao.auditoria
 WHERE cod_acao = 2231;

DELETE FROM administracao.acao
 WHERE cod_acao = 2231;


