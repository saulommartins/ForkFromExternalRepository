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
* $Id: GA_1930.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.0
*/

--------------------------------------------------------------------
-- DROPANDO ANTIGAS ESTRUTURAS DO ORGANOGRAMA - MODULO ADMINISTRACAO
-- 20090112 --------------------------------------------------------

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN (
                        SELECT cod_acao
                          FROM administracao.acao
                         WHERE cod_funcionalidade IN (3,4,5,6,26)
                   );

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN (
                        SELECT cod_acao
                          FROM administracao.acao
                         WHERE cod_funcionalidade IN (3,4,5,6,26)
                   );

DELETE
  FROM protocolo.assunto_acao
 WHERE cod_acao IN (
                        SELECT cod_acao
                          FROM administracao.acao
                         WHERE cod_funcionalidade IN (3,4,5,6,26)
                   );

DELETE
  FROM arrecadacao.acao_modelo_carne
 WHERE cod_acao IN (
                        SELECT cod_acao
                          FROM administracao.acao
                         WHERE cod_funcionalidade IN (3,4,5,6,26)
                   );

DELETE
  FROM administracao.relatorio_acao
 WHERE cod_acao IN (
                        SELECT cod_acao
                          FROM administracao.acao
                         WHERE cod_funcionalidade IN (3,4,5,6,26)
                   );

DELETE
  FROM administracao.modelo_arquivos_documento
 WHERE cod_acao IN (
                        SELECT cod_acao
                          FROM administracao.acao
                         WHERE cod_funcionalidade IN (3,4,5,6,26)
                   );

DELETE
  FROM administracao.acao
 WHERE cod_funcionalidade IN (3,4,5,6,26);

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade IN (3,4,5,6,26);

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN ( 2424
                   , 2425
                   , 2429
                   , 164
                   , 124
                   );

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN ( 2424
                   , 2425
                   , 2429
                   , 164
                   , 124
                   );

DELETE
  FROM administracao.acao
 WHERE cod_acao IN ( 2424
                   , 2425
                   , 2429
                   , 164
                   , 124
                   );


DROP SCHEMA ppa CASCADE ;

GRANT ALL ON ORGANOGRAMA.VW_ORGAO_NIVEL TO GROUP URBEM;


-------------------------------
-- CORRECAO NO NOME DA AÇÂO 700
-- 20090209 - GELSON ----------

UPDATE administracao.acao SET nom_acao = 'Alterar Organograma' WHERE cod_acao = 700;
