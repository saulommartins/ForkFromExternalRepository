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
* Versao 2.02.2
*
* Eduardo Schitz - 20140313
*
*/

----------------
-- Ticket #21523
----------------

--Deleta as permissões das ações do módulo CSE
DELETE FROM administracao.permissao 
      WHERE cod_acao IN (SELECT cod_acao 
                           FROM administracao.acao 
                          WHERE cod_funcionalidade IN (SELECT cod_funcionalidade 
                                                         FROM administracao.funcionalidade 
                                                        WHERE cod_modulo = 11
                                                      )
                        );

--Desativa as ações das funcionalidades do módulo CSE
UPDATE administracao.acao 
   SET ativo = false
 WHERE cod_funcionalidade IN (SELECT cod_funcionalidade 
                                FROM administracao.funcionalidade 
                               WHERE cod_modulo = 11
                                  );

--Desativa as funcionalidades do módulo CSE
UPDATE administracao.funcionalidade 
   SET ativo = false
 WHERE cod_modulo = 11;

--Desativa o módulo CSE
UPDATE administracao.modulo 
   SET ativo = false
 WHERE cod_modulo = 11;