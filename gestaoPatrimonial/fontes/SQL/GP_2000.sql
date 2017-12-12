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
* Versão 2.00.0
*/

----------------
-- Ticket #17577
----------------

ALTER TABLE almoxarifado.natureza_lancamento ADD COLUMN numcgm_usuario INTEGER;
UPDATE almoxarifado.natureza_lancamento  SET numcgm_usuario = 0;
ALTER  TABLE almoxarifado.natureza_lancamento ALTER COLUMN numcgm_usuario SET NOT NULL;
ALTER  TABLE almoxarifado.natureza_lancamento ADD CONSTRAINT fk_natureza_lancamento_3 FOREIGN KEY                      (numcgm_usuario)
                                                                                      REFERENCES administracao.usuario (numcgm);


----------------
-- Ticket #17760
----------------

INSERT INTO compras.modalidade (cod_modalidade, descricao) VALUES (10, 'Chamada Pública'   );
INSERT INTO compras.modalidade (cod_modalidade, descricao) VALUES (11, 'Registro de Preços');


----------------
-- Ticket #17733
----------------

ALTER TABLE compras.cotacao_item ALTER COLUMN quantidade TYPE NUMERIC(14,4);


----------------
-- Ticket #17804
----------------

ALTER TABLE compras.julgamento                               ALTER timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;
ALTER TABLE compras.cotacao_fornecedor_item_desclassificacao ALTER timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;

