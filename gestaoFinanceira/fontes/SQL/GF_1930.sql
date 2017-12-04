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
* $Id: GF_1930.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.0
*/

----------------
-- Ticket #13731
----------------

INSERT INTO administracao.relatorio 
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
     VALUES ( 2
          , 9
          , 1
          , 'Balanço Orçamentário'
          , 'balancoOrcamentario.rptdesign');


----------------
-- Ticket #13778
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 2
         , 10
         , 7
         , 'Emitir Ordem de Pagamento'
         , 'emitirOrdemPagamento.rptdesign'
         );


ALTER TABLE tesouraria.recibo_extra ALTER COLUMN cod_terminal DROP NOT NULL;
ALTER TABLE tesouraria.recibo_extra ALTER COLUMN timestamp_terminal DROP NOT NULL;
ALTER TABLE tesouraria.recibo_extra ALTER COLUMN cgm_usuario DROP NOT NULL;
ALTER TABLE tesouraria.recibo_extra ALTER COLUMN timestamp_usuario DROP NOT NULL;


----------------
-- Ticket #13803
----------------

UPDATE administracao.auditoria
   SET cod_acao = 1645
 WHERE cod_acao = 225;

UPDATE administracao.acao
   SET nom_acao = 'Incluir Lançamento'
 WHERE cod_acao = 1645;

UPDATE administracao.auditoria
   SET cod_acao = 1646
 WHERE cod_acao = 226;

UPDATE administracao.acao
   SET nom_acao = 'Alterar Lançamento'
 WHERE cod_acao = 1646;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 225;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 225;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 226;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 226;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 230;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 230;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 230;


----------------
-- Ticket #13825
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2389
          , 395
          , 'FLContaDestinacao.php'
          , 'incluir'
          , 13
          , ''
          , 'Criar Contas Contábeis'
          );

