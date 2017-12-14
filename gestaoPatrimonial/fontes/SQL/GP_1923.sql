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
* $Id: GP_1923.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.3
*/

-------------------------------------------------------------------------
-- SETANDO VALOR DEFAULT NA COLUNA timestamp EM licitacao.membro_excluido
-------------------------------------------------------------------------

ALTER TABLE licitacao.membro_excluido ALTER COLUMN timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;


----------------
-- Ticket #
----------------

CREATE TABLE almoxarifado.lancamento_manutencao_frota (
    cod_lancamento          INTEGER             NOT NULL,
    cod_item                INTEGER             NOT NULL,
    cod_marca               INTEGER             NOT NULL,
    cod_almoxarifado        INTEGER             NOT NULL,
    cod_centro              INTEGER             NOT NULL,
    cod_manutencao          INTEGER             NOT NULL,
    exercicio               CHAR(4)             NOT NULL,   
    CONSTRAINT pk_lancamento_manutencao_frota   PRIMARY KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_lancamento_manutencao_frota_1 FOREIGN KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)
                                                REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_lancamento_manutencao_frota_2 FOREIGN KEY                                 (cod_manutencao, exercicio)
                                                REFERENCES frota.manutencao                 (cod_manutencao, exercicio)
);

GRANT ALL ON almoxarifado.lancamento_manutencao_frota TO GROUP urbem;

