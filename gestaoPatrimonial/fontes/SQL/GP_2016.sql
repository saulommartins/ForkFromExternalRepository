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
* Versao 2.01.6
*
* Fabio Bertoldi - 20130416
*
*/

----------------
-- Ticket #19914
----------------

UPDATE administracao.acao SET cod_funcionalidade = 28, ordem = 4 where cod_acao = 2408;

UPDATE administracao.acao SET ordem =  1 WHERE cod_acao =  112;
UPDATE administracao.acao SET ordem =  2 WHERE cod_acao =  824;
UPDATE administracao.acao SET ordem =  3 WHERE cod_acao = 1111;
UPDATE administracao.acao SET ordem =  5 WHERE cod_acao =  108;
UPDATE administracao.acao SET ordem =  6 WHERE cod_acao =  111;
UPDATE administracao.acao SET ordem =  7 WHERE cod_acao =  819;
UPDATE administracao.acao SET ordem =  8 WHERE cod_acao = 1110;
UPDATE administracao.acao SET ordem =  9 WHERE cod_acao =  110;
UPDATE administracao.acao SET ordem = 10 WHERE cod_acao =  125;
UPDATE administracao.acao SET ordem = 11 WHERE cod_acao = 2784;
UPDATE administracao.acao SET ordem = 12 WHERE cod_acao = 2776;
UPDATE administracao.acao SET ordem = 13 WHERE cod_acao = 2780;
UPDATE administracao.acao SET ordem = 14 WHERE cod_acao = 2407;
UPDATE administracao.acao SET ordem = 15 WHERE cod_acao =  113;
UPDATE administracao.acao SET ordem = 16 WHERE cod_acao =  106;
UPDATE administracao.acao SET ordem = 17 WHERE cod_acao = 2183;
