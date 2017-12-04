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
* $Id: GT_1972.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.97.2
*/

----------------------------------------------------------
-- INCLUSAO DE MODELOS DE DOCUMENTOS - MODULO FISCALIZACAO
-- 20090216 - Heleno Menezes dos Santos ------------------

INSERT
  INTO administracao.modelo_documento
VALUES (
         ( SELECT MAX (cod_documento) + 1
             FROM administracao.modelo_documento )
     , 'Termo de Baixa/Inutilização de Notas Fiscais'
     , 'baixa_nota.odt'
     , 2
     );
INSERT
  INTO administracao.arquivos_documento
VALUES (
         ( SELECT MAX (cod_arquivo) + 1
             FROM administracao.arquivos_documento )
     , 'baixa_nota.odt'
     , 'ddd73d0219148709664b5600641befcd'
     , true
     );
INSERT
  INTO administracao.modelo_arquivos_documento
VALUES ( 2283
     , ( SELECT MAX (cod_documento)
           FROM administracao.modelo_documento )
     , ( SELECT MAX (cod_arquivo)
           FROM administracao.arquivos_documento )
     , true
     , true
     , 2
     );

INSERT
  INTO administracao.modelo_documento
VALUES (
         ( SELECT MAX (cod_documento) + 1
             FROM administracao.modelo_documento )
     , 'Termo de Autorização de Impressão de Notas Fiscais'
     , 'autorizacao_impressao_doc_fiscal.odt'
     , 2
     );
INSERT
  INTO administracao.arquivos_documento
VALUES (
         ( SELECT MAX (cod_arquivo) + 1
             FROM administracao.arquivos_documento )
     , 'autorizacao_impressao_doc_fiscal.odt'
     , '42eee412312a074fc504341fa718bd93'
     , true
     );
INSERT
  INTO administracao.modelo_arquivos_documento
VALUES ( 2282
     , ( SELECT MAX (cod_documento)
           FROM administracao.modelo_documento )
     , ( SELECT MAX (cod_arquivo)
           FROM administracao.arquivos_documento )
     , true
     , true
     , 2
     );

INSERT
  INTO administracao.modelo_documento
VALUES (
         ( SELECT MAX (cod_documento) + 1
             FROM administracao.modelo_documento )
     , 'Termo de Recebimento de Documentos'
     , 'termo_recebimento.odt'
     , 2
     );
INSERT
  INTO administracao.arquivos_documento
VALUES (
         ( SELECT MAX (cod_arquivo) + 1
             FROM administracao.arquivos_documento )
     , 'termo_recebimento.odt'
     , 'c05c5024a4c954ad1a612357b44fa7e0'
     , true
     );
INSERT
  INTO administracao.modelo_arquivos_documento
VALUES ( 2300
     , ( SELECT MAX (cod_documento)
           FROM administracao.modelo_documento )
     , ( SELECT MAX (cod_arquivo)
           FROM administracao.arquivos_documento )
     , true
     , true
     , 2
     );

INSERT
  INTO administracao.modelo_documento
VALUES (
         ( SELECT MAX (cod_documento) + 1
             FROM administracao.modelo_documento )
     , 'Termo de Devolução de Documentos'
     , 'termo_devolucao.odt'
     , 2
     );
INSERT
  INTO administracao.arquivos_documento
VALUES (
         ( SELECT MAX (cod_arquivo) + 1
             FROM administracao.arquivos_documento )
     , 'termo_devolucao.odt'
     , '5512208b6bf1f0b9bea4cf827a07e914'
     , true
     );
INSERT
  INTO administracao.modelo_arquivos_documento
VALUES ( 2301
     , ( SELECT MAX (cod_documento)
           FROM administracao.modelo_documento )
     , ( SELECT MAX (cod_arquivo)
           FROM administracao.arquivos_documento )
     , true
     , true
     , 2
     );


----------------
-- Ticket #14626
----------------

ALTER TABLE arrecadacao.pagamento_diferenca_compensacao DROP CONSTRAINT pk_pagamento_diferenca_compensacao;
ALTER TABLE arrecadacao.pagamento_diferenca_compensacao ADD  CONSTRAINT pk_pagamento_diferenca_compensacao 
                                                             PRIMARY KEY (cod_compensacao, numeracao, ocorrencia_pagamento, cod_convenio, cod_calculo);


----------------
-- Ticket #14589
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2477
          , 178
          , 'LSCancelarDesmembramentoLote.php'
          , 'Cancelar'
          , 10
          , ''
          , 'Cancelar Desmembramento'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2478
          , 193
          , 'LSCancelarDesmembramentoLote.php'
          , 'Cancelar'
          , 10
          , ''
          , 'Cancelar Desmembramento'
          );


----------------
-- Ticket #14692
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 5
          , 33
          , 5
          , 'Extrato de Dívida Ativa'
          , 'extratoDividaAtivaAnalitico.rptdesign'
          );

