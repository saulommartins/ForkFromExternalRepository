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
* $Id: GP_1921.sql 38406 2009-03-02 12:11:45Z gelson $
*
* Versão 1.92.1
*/

----------------
-- Ticket #12841
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2416
          , 326
          , 'FMManterSuspensaoEdital.php'
          , 'incluir'
          , 16
          , ''
          , 'Suspender Edital'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2417
          , 326
          , 'FMManterSuspensaoEdital.php'
          , 'anular'
          , 17
          , ''
          , 'Anular Suspensão de Edital'
          );

----------------
-- Ticket #12903
----------------

UPDATE administracao.acao
   SET ordem = ordem + 1
 WHERE cod_funcionalidade = 326
   AND ordem > 4;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2427
          , 326
          , 'FLTermoAutuacaoEdital.php'
          , 'incluir'
          , 5
          , ''
          , 'Termo de Autuação de Edital'
          );

----------------
-- Ticket #14510
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2469
          , 291
          , 'FLReemitirSaida.php'
          , 'reemitir'
          , 90
          , ''
          , 'Reemitir Saída'
          );


----------------
-- Ticket #14509
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2470
          , 290
          , 'FLReemitirEntrada.php'
          , 'reemitir'
          , 90
          , ''
          , 'Reemitir Entrada'
          );





