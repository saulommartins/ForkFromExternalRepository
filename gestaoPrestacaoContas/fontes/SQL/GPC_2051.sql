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
* Versao 2.05.1
*
* Fabio Bertoldi - 20160503
*
*/

----------------
-- Ticket #22350
----------------

UPDATE tcmgo.tipo_orgao SET descricao = 'PODER EXECUTIVO'                             WHERE cod_tipo =  1;
UPDATE tcmgo.tipo_orgao SET descricao = 'PODER LEGISLATIVO'                           WHERE cod_tipo =  2;
UPDATE tcmgo.tipo_orgao SET descricao = 'FUNDEF/FUNDEB'                               WHERE cod_tipo =  3;
UPDATE tcmgo.tipo_orgao SET descricao = 'ADM. DIRETA - FUNDO ESPECIAL'                WHERE cod_tipo =  4;
UPDATE tcmgo.tipo_orgao SET descricao = 'ADM. INDIRETA - AUTARQUIA'                   WHERE cod_tipo =  5;
UPDATE tcmgo.tipo_orgao SET descricao = 'ADM. INDIRETA - FUNDAÇÃO'                    WHERE cod_tipo =  6;
UPDATE tcmgo.tipo_orgao SET descricao = 'EMPRESA PÚBLICA'                             WHERE cod_tipo =  7;
UPDATE tcmgo.tipo_orgao SET descricao = 'SOCIEDADE DE ECONOMIA MISTA'                 WHERE cod_tipo =  8;
UPDATE tcmgo.tipo_orgao SET descricao = 'RPPS (REGIME PRÓPRIO DE PREVIDÊNCIA SOCIAL)' WHERE cod_tipo =  9;
UPDATE tcmgo.tipo_orgao SET descricao = 'FMS - FUNDO MUNICIPAL DE SAÚDE'              WHERE cod_tipo = 10;

INSERT INTO tcmgo.tipo_orgao (cod_tipo, descricao) VALUES (11, 'FMAS - FUNDO MUNICIPAL DE ASSISTÊNCIA SOCIAL'   );
INSERT INTO tcmgo.tipo_orgao (cod_tipo, descricao) VALUES (12, 'FMCA - FUNDO MUNICIPAL DA CRIANÇA E ADOLESCENTE');
INSERT INTO tcmgo.tipo_orgao (cod_tipo, descricao) VALUES (13, 'FMH - FUNDO MUNICIPAL DE HABITAÇÃO'             );
INSERT INTO tcmgo.tipo_orgao (cod_tipo, descricao) VALUES (14, 'FME - FUNDO MUNICIPAL DE EDUCAÇÃO'              );



